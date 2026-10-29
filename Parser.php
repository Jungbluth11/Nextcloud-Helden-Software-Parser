<?php
declare(strict_types=1);

namespace OCA\Xmlviewer\Parser\Helden;

use OCA\Xmlviewer\Parser\XmlParserInterface;

class Parser implements XmlParserInterface {
	public function supportsRoot(string $rootNode): bool {
		return $rootNode === "helden";
	}

	public function getTemplatePath(): string {
		return __DIR__ . "/template.php";
	}

	public function parse(\SimpleXMLElement $xml, string $source): ?array {
		if (!$this->supportsRoot($xml->getName()) || !isset($xml->held[0])) {
			return null;
		}

		$held = $xml->held[0];
		$basis = $held->basis[0];
		$properties = $held->eigenschaften[0];
		$gefaessDerSterne = false;
		$profession = "";
		$fkBase = 0;
		
		foreach ($basis->ausbildungen->ausbildung as $training) {
			if ((string)$training["art"] === "Hauptprofession") {
				$profession = (string)$training["string"];
				break;
			}
		}
		
		foreach ($held->sf->sonderfertigkeit as $special) {
			if ((string)$special["name"] === "Gefäß der Sterne") {
				$gefaessDerSterne = true;
				break;
			}
		}
		
		$rawAbilities = [];
		
		foreach ($properties->eigenschaft as $property) {
			$rawAbilities[(string)$property["name"]] = $property;
		}

		$modified = static function (string $name) use ($rawAbilities): int {
			$property = $rawAbilities[$name];
			
			return (int)$property["value"] + (int)$property["mod"];
		};

		$abilities = [];
		
		foreach ($properties->eigenschaft as $property) {
			$name = (string)$property["name"];
			if (!isset($property["mod"])) {
				continue;
			}

			$value = (int)$property["value"] + (int)$property["mod"];
			if ($name === "Lebensenergie") {
				$value = intdiv(2 * $modified("Konstitution") + $modified("Körperkraft"), 2) + $modified("Lebensenergie");
			} elseif ($name === "Ausdauer") {
				$value = intdiv($modified("Mut") + $modified("Konstitution") + $modified("Gewandtheit"), 2) + $modified("Ausdauer");
			} elseif ($name === "Magieresistenz") {
				$value = (int)ceil(($modified("Mut") + $modified("Klugheit") + $modified("Konstitution")) / 5) + $modified("Magieresistenz");
			} elseif ($name === "Astralenergie") {
				$value = (int)$property["mod"] === 0
					? 0
					: (int)ceil(($modified("Mut") + $modified("Intuition") + ($gefaessDerSterne ? 2 : 1) * $modified("Charisma")) / 2) + $modified("Astralenergie") + (int)$property["grossemeditation"];
			}
			
			if($name == "fk"){
				$fkBase = $value;
			}
			
			$name = match ($name) {
				"ini" => "Initiative", "at" => "Attacke", "pa" => "Parade", "fk" => "Fernkampf-Basis", default => $name,
			};
						
			$abilities[] = ["name" => $name, "value" => $value];
		}

		$talents = [];
		
		foreach ($held->talentliste->talent as $talent) {
			$talents[] = ["name" => (string)$talent["name"], "value" => (int)$talent["value"]];
		}

		$spells = [];
		
		foreach ($held->zauberliste->zauber as $spell) {
			$name = (string)$spell["name"];
			$variant = (string)$spell["variante"];
			
			if ($variant !== "") {
				$name .= " (" . $variant . ")";
			}
			
			$spells[] = ["name" => $name, "value" => (int)$spell["value"], "representation" => (string)$spell["repraesentation"]];
		}
		
		$fightStats = [];
		$distanceWeapons = [
			"Armbrust",
			"Bela",
			"Belagerungswaffen",
			"Blasrohr",
			"Bogen",
			"Diskus",
			"Feuerwaffen",
			"Schleuder",
			"Wurfbeile",
			"Wurfmesser",
			"Wurfspeere"
			];
		
		foreach($talents as $talent){
			if(in_array($talent["name"], $distanceWeapons)){
			$fightStats[] = ["name" => $talent["name"], "at" => $talent["value"] + $fkBase, "pa" => ""];
			}
		}
		
		foreach ($held->kampf->kampfwerte as $stat) {
			$fightStats[] = ["name" => (string)$stat["name"], "at" => (string)$stat->attacke["value"], "pa" => (string)$stat->parade["value"]];
		}

		$specials = [];
		
		foreach ($held->sf->sonderfertigkeit as $special) {
			$name = (string)$special["name"];
			$children = iterator_to_array($special->children());
			
			if ($children !== [] && !str_contains($name, "Talentspezialisierung") && !str_contains($name, "Zauberspezialisierung")) {
				foreach ($children as $child) {
					$childName = (string)$child["name"];
					
					if (in_array($name, ["Waffenmeister", "Berufsgeheimnis"], true)) {
						$nested = [];
						
						foreach ($child->children() as $grandchild) {
							$nested[] = (string)$grandchild["name"];
						}
						
						$childName = implode("; ", array_slice($nested, 0, $name === "Berufsgeheimnis" ? 3 : 2));
					}
					
					if ($childName !== "") {
						$specials[] = $name . " (" . $childName . ")";
					}
				}
			} else {
				$specials[] = $name;
			}
		}
		
		$specials = array_values(array_unique($specials));
		$perks = [];
		
		foreach ($held->vt->vorteil as $perk) {
			$name = (string)$perk["name"];
			$value = (string)$perk["value"];

			$selections = $perk->children();
			if (isset($selections[1])) {
				$value = (string)$selections[1]["value"] . " ". (string)$selections[0]["value"];
			}

			$perks[] = $name . " " .$value;
		}

		$items = [];
		
		foreach ($held->gegenstände->gegenstand as $item) {
			$details = [];
								
			$amount = (string)$item["anzahl"];
			
			$name = isset($item->modallgemein->name["value"])
				? (string)$item->modallgemein->name["value"]
				: (string)$item["name"];
				
			$weight = isset($item->modallgemein->gewicht["value"])
				? (string)$item->modallgemein->gewicht["value"]
				: "";
				
			$items[] = ["amount" => $amount, "name" => $name, "weight" => $weight];
		}

		return [
			"apTotal" => (string)$basis->abenteuerpunkte["value"],
			"apFree" => (string)$basis->freieabenteuerpunkte["value"],
			"name" => (string)$held["name"],
			"gender" => (string)$basis->geschlecht["name"],
			"race" => (string)$basis->rasse["string"],
			"profession" => $profession,
			"height" => str_replace(".", ",",(string)((float)$basis->rasse->groesse["value"] / 100)),
			"weight" => (string)$basis->rasse->groesse["gewicht"],
			"age" => (string)$basis->rasse->aussehen["alter"],
			"culture" => (string)$basis->kultur["string"],
			"eyecolor" => (string)$basis->rasse->aussehen["augenfarbe"],
			"haircolor" => (string)$basis->rasse->aussehen["haarfarbe"],
			"guild" => isset($basis->gilde) ? (string)$basis->gilde["name"] : "",
			"abilities" => $abilities,
			"talents" => $talents,
			"spells" => $spells,
			"fightstats" => $fightStats,
			"specials" => $specials,
			"perks" => $perks,
			"items" => $items,
		];
	}
}

return new Parser();
