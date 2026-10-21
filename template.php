<?php
$character = $_["data"];
$professionPrefix = $character["guild"] !== "" ? "Magier: " : "";
$so = "";
$col1 = [];
$col2 = [];

foreach($character["abilities"] as $ability){
	switch($ability["name"]){
		case "Sozialstatus":
			$so = $ability["value"];
			break;
		case "Initiative":
		case "Attacke":
		case "Parade":
		case "Fernkampf-Basis":
			$col2[] = $ability;
			break;
		default:
		$col1[] = $ability;
			break;
	}
}


$sections = [
	["Allgemein", []],
	["Eigenschaften", []],
	["Talente", $character["talents"]],
	["Zauber", $character["spells"]],
	["Kampfwerte", $character["fightstats"]],
	["Inventar/Tiere", $character["items"]]
];
?>
	<nav class="xmlviewer-tabs" role="tablist">
		<?php foreach ($sections as $index => [$sectionTitle, $items]): ?>
		<button type="button" role="tab" id="xmlviewer-tab-<?php p((string)$index); ?>" aria-controls="xmlviewer-panel-<?php p((string)$index); ?>" aria-selected="<?php p($index === 0 ? 'true' : 'false'); ?>" tabindex="<?php p($index === 0 ? '0' : '-1'); ?>"><?php p($sectionTitle); ?></button>
		<?php endforeach; ?>
	</nav>
	<div class="xmlviewer-panels">
		<?php foreach ($sections as $index => [$sectionTitle, $items]): ?>
		<section class="xmlviewer-card" role="tabpanel" id="xmlviewer-panel-<?php p((string)$index); ?>" aria-labelledby="xmlviewer-tab-<?php p((string)$index); ?>"<?php if ($index !== 0): ?> hidden="hidden"<?php endif; ?>>
			<?php if ($sectionTitle === "Allgemein"): ?>
			<div>
				<b>Name:</b> <?php p($character["name"]); ?><br>
				<b>Rasse:</b> <?php p($character["race"]); ?><br>
				<b>Kultur:</b> <?php p($character["culture"]); ?><br>
				<b>Profession:</b> <?php p($professionPrefix . $character["profession"]); ?><br>
			</div>	
			<div class="xmlviewer-flex-container">
				<div>
					<table>
						<tbody>
							<tr>
							<td>Abenteuerpunkte:</td>
							<td><?php p($character["apTotal"]); ?></td>
							</tr>
							<tr>
							<td>AP Guthaben:</td>
							<td><?php p($character["apFree"]); ?></td>
							</tr>
							<tr>
							<td>Geschlecht:</td>
							<td><?php p($character["gender"]); ?></td>
							</tr>
							<tr>
							<td>Haarfarbe:</td>
							<td><?php p($character["haircolor"]); ?></td>
							</tr>
						</tbody>
					</table>
				</div>
				<div>
					<table>
						<tbody>
							<tr>
							<td>Größe:</td>
							<td><?php p($character["height"]); ?> Schritt</td>
							</tr>
							<tr>
							<td>Gewicht:</td>
							<td><?php p($character["weight"]); ?> Stein</td>
							</tr>
							<tr>
							<td>Alter:</td>
							<td><?php p($character["age"]); ?> Jahre</td>
							</tr>
							<tr>
							<td>Augenfarbe:</td>
							<td><?php p($character["eyecolor"]); ?></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<br>
			<div class="xmlviewer-flex-container gap-50">
				<div class="xmlviewer-table-wrap xmlviewer-table-single-column">
					<table>
						<thead>
							<tr>
								<th><center><h3>Vor- und Nachteile</h3></center></th>
							</tr>
						</thead>
						<tbody>
						<?php foreach ($character["perks"] as $perk): ?>
							<tr>
								<td><?php p($perk); ?></td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				</div>
				<div class="xmlviewer-table-wrap xmlviewer-table-single-column">
					<table>
						<thead>
							<tr>
								<th><center><h3>Sonderfertigkeiten</h3></center></th>
							</tr>
						</thead>

						<tbody>
						<?php foreach ($character["specials"] as $special): ?> 
							<tr>
								<td><?php p($special); ?></td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
			<?php elseif ($sectionTitle === "Eigenschaften"): ?>
			<div class="xmlviewer-flex-container gap-100">
				<div>
					<table class="xmlviewer-table-50 no-table">
						<tbody>
						<?php foreach ($col1 as $col): ?>
							<tr>
								<td><b><?php p($col["name"]); ?>:</b></td>
								<td><?php p($col["value"] === 0 ? "" : $col["value"]); ?></td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				</div>
				<div>
					<table class="xmlviewer-table-50 no-table">
						<tbody>
						<?php foreach ($col2 as $col): ?>
							<tr>
								<td><b><?php p($col["name"]); ?>:</b></td>
								<td><?php p($col["value"]); ?></td>
							</tr>
						<?php endforeach; ?>
							<tr>
								<td><p></p></td>
								<td><p></p></td>
							</tr>
							<tr>
								<td>Sozialstatus:</td>
								<td><?php p($so); ?></td>
							</tr>
							<tr>
								<td>Gilde:</td>
								<td><?php p($character["guild"]); ?></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>			
			<?php elseif ($sectionTitle === "Talente" || $sectionTitle === "Zauber"): ?>
			<div class="xmlviewer-table-wrap">
				<table class="xmlviewer-table-100">
					<thead>
						<tr>
							<th>Name</th>
							<th>Wert</th>
							<?php if ($sectionTitle === "Zauber"): ?>
							<th>Repräsentation</th>
							<?php endif; ?>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($items as $item): ?>
						<tr>
							<td><?php p($item["name"]); ?></td>
							<td><?php p((string)$item["value"]); ?></td>
							<?php if ($sectionTitle === "Zauber"): ?>
							<td><?php p($item["representation"]); ?></td>
							<?php endif; ?>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			</div>
			<?php elseif ($sectionTitle === "Kampfwerte"): ?>
			<div class="xmlviewer-table-wrap">
				<table class="xmlviewer-table-100">
					<thead>
						<tr>
							<th>Fähigkeit</th>
							<th>Attacke</th>
							<th>Parade</th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($items as $item): ?>
						<tr>
							<td><?php p($item["name"]); ?></td>
							<td><?php p((string)$item["at"]); ?></td>
							<td><?php p($item["pa"]); ?></td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			</div>
			<?php elseif ($sectionTitle === "Inventar/Tiere"): ?>
			<div class="xmlviewer-table-wrap">
				<table class="xmlviewer-table-100">
					<thead>
						<tr>
							<th>Anzahl</th>
							<th>Name</th>
							<th>Gewicht</th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($items as $item): ?>
						<tr>
							<td><?php p($item["amount"]); ?></td>
							<td><?php p($item["name"]); ?></td>
							<td><?php p($item["weight"]); ?></td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			</div>
			<?php endif; ?>
			<?php if ($index > 2 && count($items) === 0): ?>
			<br><p class="xmlviewer-empty">&nbsp; Keine Einträge</p>
			<?php endif; ?>
		</section>
		<?php endforeach; ?>
	</div>
