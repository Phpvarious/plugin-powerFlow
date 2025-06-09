<?php
//ini_set('display_errors', 0);
if (!isConnect('admin')) {
	throw new Exception('{{401 - Accès non autorisé}}');
}
sendVarToJS('eqType', 'powerFlow');
$eqLogics = eqLogic::byType('powerFlow');
?>

<div class="row row-overflow">
	<div class="col-xs-12 eqLogicThumbnailDisplay">
		<legend><i class="fas fa-cog"></i> {{Gestion}}</legend>
		<div class="eqLogicThumbnailContainer">
			<div class="cursor eqLogicAction logoPrimary" data-action="add">
				<i class="fas fa-plus-circle"></i>
				<br>
				<span>{{Ajouter}}</span>
			</div>
			<div class="cursor eqLogicAction logoSecondary" data-action="gotoPluginConf">
				<i class="fas fa-wrench"></i><br>
				<span>{{Configuration}}</span>
			</div>
		</div>
		<legend><i class="fas fas fa-bolt"></i> {{Power Flow}}</legend>
		<?php
		if (count($eqLogics) == 0) {
			echo '<div class="alert alert-info text-center" style="width: 100%; background-color: var(--al-info-color) !important;">{{Aucun équipement trouvé}}</div>';
		} else {
			echo '<div class="input-group" style="margin:5px;">';
			echo '<input class="form-control roundedLeft" placeholder="{{Rechercher}}" id="in_searchEqlogic" />';
			echo '<div class="input-group-btn">';
			echo '<a id="bt_resetSearch" class="btn" style="width:30px"><i class="fas fa-times"></i></a>';
			echo '<a class="btn roundedRight hidden" id="bt_pluginDisplayAsTable" data-coreSupport="1" data-state="0"><i class="fas fa-grip-lines"></i></a>';
			echo '</div>';
			echo '</div>';
			echo '<div class="eqLogicThumbnailContainer">';
			foreach ($eqLogics as $eqLogic) {
				$opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
				echo '<div class="eqLogicDisplayCard cursor ' . $opacity . '" data-eqLogic_id="' . $eqLogic->getId() . '">';
				/* getImage : 
					core 4.4 - returns plugin image
					core 4.5 - returns the custom image if exist or else the plugin image 
				*/
				echo '<img src="' . $eqLogic->getImage() . '" height="105" width="95">';
				echo '<br>';
				echo '<span class="name">' . $eqLogic->getHumanName(true, true) . '</span>';
				echo '<span class="hiddenAsCard displayTableRight hidden">';
				echo ($eqLogic->getIsVisible() == 1) ? '<i class="fas fa-eye" title="{{Équipement visible}}"></i>' : '<i class="fas fa-eye-slash" title="{{Équipement non visible}}"></i>';
				echo '</span>';
				echo '</div>';
			}
			echo '</div>';
		}
		?>
	</div>

	<div class="col-xs-12 eqLogic" style="display: none;">
		<div class="input-group pull-right" style="display:inline-flex">
			<span class="input-group-btn">
				<a class="btn btn-sm btn-default eqLogicAction roundedLeft" data-action="configure"><i class="fas fa-cogs"></i> {{Configuration avancée}}
				</a><a class="btn btn-sm btn-success eqLogicAction" data-action="save"><i class="fas fa-check-circle"></i> {{Sauvegarder}}
				</a><a class="btn btn-sm btn-danger eqLogicAction roundedRight" data-action="remove"><i class="fas fa-minus-circle"></i> {{Supprimer}}
				</a>
			</span>
		</div>
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation"><a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay"><i class="fas fa-arrow-circle-left"></i></a></li>
			<li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-tachometer-alt"></i><span class="hidden-xs"> {{Équipement}}</span></a></li>
			<li role="presentation"><a href="#configureInverter" aria-controls="home" role="tab" data-toggle="tab"><i class="icon mdi-application-cog"></i><span class="hidden-xs"> {{Onduleur}}</span></a></li>
			<li role="presentation"><a href="#configureGrid" aria-controls="home" role="tab" data-toggle="tab"><i class="mdi-transmission-tower"></i><span class="hidden-xs"> {{Réseau}}</span></a></li>
			<li role="presentation"><a href="#configureSolar" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-solar-panel"></i><span class="hidden-xs"> {{Solaire}}</span></a></li>
			<li role="presentation"><a href="#configureBattery" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-battery-half"></i><span class="hidden-xs"> {{Batterie}}</span></a></li>
			<li role="presentation"><a href="#configureLoad" aria-controls="home" role="tab" data-toggle="tab"><i class="icon techno-oven4"></i><span class="hidden-xs"> {{Récepteurs}}</span></a></li>
			<li role="presentation"><a href="#configureAux" aria-controls="home" role="tab" data-toggle="tab"><i class="icon jeedomapp-reload-manuel"></i><span class="hidden-xs"> {{Générateur}}</span></a></li>
			<li role="presentation"><a href="#configurePerso" aria-controls="home" role="tab" data-toggle="tab"><i class="icon fas fa-cogs"></i><span class="hidden-xs"> {{Perso}}</span></a></li>
			<!--<li role="presentation"><a href="#commandtab" aria-controls="home" role="tab" aria-controls="profile" data-toggle="tab"><i class="fas fa-list"></i> {{Commandes}}</a></li>-->
		</ul>
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane active" id="eqlogictab">
				<form class="form-horizontal">
					<fieldset>
						<div class="col-lg-6">
							<legend><i class="fas fa-wrench"></i> {{Paramètres généraux}}</legend>
							<div class="form-group">
								<label class="col-sm-4 control-label">{{Nom de l'équipement}}</label>
								<div class="col-sm-6">
									<input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display:none;">
									<input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l'équipement}}">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">{{Objet parent}}</label>
								<div class="col-sm-6">
									<select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
										<option value="">{{Aucun}}</option>
										<?php
										$options = '';
										foreach ((jeeObject::buildTree(null, false)) as $object) {
											$options .= '<option value="' . $object->getId() . '">' . str_repeat('&nbsp;&nbsp;', $object->getConfiguration('parentNumber')) . $object->getName() . '</option>';
										}
										echo $options;
										?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">{{Catégorie}}</label>
								<div class="col-sm-6">
									<?php
									foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
										echo '<label class="checkbox-inline">';
										echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" >' . $value['name'];
										echo '</label>';
									}
									?>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">{{Options}}</label>
								<div class="col-sm-6">
									<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked>{{Activé}}</label>
									<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked>{{Visible}}</label>
								</div>
							</div>
							<legend><i class="fas fa-cogs"></i> {{Paramètres spécifiques}}</legend>
							<div class="form-group">
								<label class="col-sm-4 control-label">
									{{Clignotement}} <sup><i class="fas fa-question-circle" title="{{Activer le clignotement des éléments en alertes}}"></i></sup></label>
								<div class="col-sm-6">
									<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="blink">{{Activer}}</label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">{{Couleur warning}} <sup><i class="fas fa-question-circle" title="{{Couleur du contour des éléments en alertes}}"></i></sup></label>
								<div class="col-lg-2">
									<input type="color" class="eqLogicAttr form-control input-sm" value="#ff0000" data-l1key="configuration" data-l2key="colorWarning" />
								</div>

							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">
									{{Gauges}} <sup><i class="fas fa-question-circle" title="{{Désactiver les gauges}}"></i></sup>
								</label>
								<div class="col-sm-6">
									<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="disableGauge">{{Désactiver}}</label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">
									{{Gauge ratios}} <sup><i class="fas fa-question-circle" title="{{Désactiver la gauge des ratios}}"></i></sup>
								</label>
								<div class="col-sm-6">
									<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="disableGaugeRatio">{{Désactiver}}</label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">
									{{Formatage milliers}} <sup><i class="fas fa-question-circle" title="{{Activer le formattage des milliers}} ({{ex :}} 5000 -> 5 000)"></i></sup>
								</label>
								<div class="col-sm-6">
									<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="formatMillier">{{Activer}}</label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">
									{{Debug widget}} <sup><i class="fas fa-question-circle" title="{{Activer les logs js sur le widget}}"></i></sup>
								</label>
								<div class="col-sm-6">
									<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="debug">{{Activer}}</label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">{{Couleur arrière-plan}} </label>
								<div class="col-sm-2">
									<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="background::activate">{{Activer}}</label>
								</div>

								<div class="col-lg-2">
									<input type="color" class="eqLogicAttr form-control input-sm" value="#000000" data-l1key="configuration" data-l2key="background::color" />
								</div>
							</div>
						</div>
					</fieldset>
				</form>
			</div>
			<div role="tabpanel" class="tab-pane" id="commandtab">
				<br><br>
				<div class="table-responsive">
					<table id="table_cmd" class="table table-bordered table-condensed">
						<thead>
							<tr>
								<th class="hidden-xs" style="min-width:50px;width:70px;">ID</th>
								<th style="min-width:400px;width:450px;">{{Nom}}</th>
								<th style="width:400px;">{{Type}}</th>
								<th style="min-width:160px;">{{Options}}</th>
								<th style="min-width:160px;">{{Valeur}}</th>
								<th style="min-width:80px;width:140px;">{{Actions}}</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
			<!-- 
			////////////////////////////
			////////  INVERTER  ////////
			//////////////////////////// 
			-->
			<div role="tabpanel" class="tab-pane" id="configureInverter">
				<div class="tab-pane">
					<br>
					<!-- INVERTER CONFIGURATION -->
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title"><i class="fas fas fa-bolt"></i> {{Configuration}}</h3>
						</div>
						<div class="panel-body">
							<form class="form-horizontal">
								<fieldset>
									<div class="form-group">
										<div class="col-lg-3">
											<div class="input-group">
												<span class="input-group-addon roundedLeft" style="min-width: 125px;">{{Couleur}}</span>
												<input type="color" class="eqLogicAttr form-control" value="#808080" data-l1key="configuration" data-l2key="inverter::color" />
												<span class="input-group-btn">
													<a class="btn btn-default restoreDefaut roundedRight" data-type="inverter::color" data-defaut="#808080" title="{{Couleur par défaut}}"><i class="fas fa-eraser"></i></a>
												</span>
											</div>
										</div>
										<div class="col-lg-3">
											<div class="input-group">
												<span class="input-group-addon roundedLeft" style="min-width: 125px;">{{Couleur texte intérieur}}</span>
												<input type="color" class="eqLogicAttr form-control" value="#000000" data-l1key="configuration" data-l2key="inverter::color::in" />
												<span class="input-group-btn">
													<a class="btn btn-default restoreDefaut roundedRight" data-type="inverter::color::in" data-defaut="#000000" title="{{Couleur par défaut}}"><i class="fas fa-eraser"></i></a>
												</span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<div class="col-lg-3">
											<div class="input-group">
												<span class="input-group-addon roundedLeft input-group-addon" style="min-width: 125px;">
													{{Icône}}
												</span>
												<input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="inverter::img" disabled />
												<span class="input-group-btn">
													<a class="btn btn-default bt_library" title="{{Bibliothèque}}" data-options="noIcon" data-type="inverter::img"><i class="fas fa-photo-video"></i></a>
												</span>
												<span class="input-group-btn">
													<a class="btn btn-default restoreDefaut roundedRight" data-type="inverter::img" data-defaut="" title="{{Icône par défaut}}">
														<i class="fas fa-eraser"></i>
													</a>
												</span>
											</div>
										</div>
										<div class="col-lg-2">
											<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="inverter::img::none">{{Aucun}}
												<sup><i class="fas fa-question-circle" title="{{Prioritaire sur l'icône}}<br>{{Affiche simplement un cadre}}"></i></sup>
											</label>
										</div>
									</div>
								</fieldset>
							</form>
						</div>
					</div>
					<!-- INVERTER OTHER -->
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title"><i class="fas fas fa-bolt"></i> {{Autres}}</h3>
						</div>
						<div class="panel-body">
							<form class="form-horizontal">
								<fieldset>
									<div class="form-group">
										<div class="col-lg-1">
											<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="inverter::voltage::desactivate">{{Désactiver}}</label>
										</div>
										<div class="col-lg-4">
											<div class="input-group">
												<span class="input-group-addon roundedLeft" style="min-width: 125px;">{{Tension}} </span>
												<input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="inverter::voltage::cmd">
												<span class="input-group-btn">
													<a class="btn btn-default listCmdInfo roundedRight" data-type="inverter::voltage::cmd" data-subtype="numeric"><i class="fas fa-list-alt"></i></a>
												</span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<div class="col-lg-1">
											<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="inverter::frenquency::desactivate">{{Désactiver}}</label>
										</div>
										<div class="col-lg-4">
											<div class="input-group">
												<span class="input-group-addon roundedLeft" style="min-width: 125px;">{{Fréquence}} </span>
												<input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="inverter::frequency::cmd">
												<span class="input-group-btn">
													<a class="btn btn-default listCmdInfo roundedRight" data-type="inverter::frequency::cmd" data-subtype="numeric"><i class="fas fa-list-alt"></i></a>
												</span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<div class="col-lg-1">
											<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="inverter::current::desactivate">{{Désactiver}}</label>
										</div>
										<div class="col-lg-4">
											<div class="input-group">
												<span class="input-group-addon roundedLeft" style="min-width: 125px;">{{Intensité}} </span>
												<input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="inverter::current::cmd">
												<span class="input-group-btn">
													<a class="btn btn-default listCmdInfo roundedRight" data-type="inverter::current::cmd" data-subtype="numeric"><i class="fas fa-list-alt"></i></a>
												</span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<div class="col-lg-1">
											<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="inverter::lcd::desactivate">{{Désactiver}}</label>
										</div>
										<div class="col-lg-4">
											<div class="input-group">
												<span class="input-group-addon roundedLeft" style="min-width: 125px;">
													{{LCD}} <sup><i class="fas fa-question-circle" title="{{Info à afficher dans le lcd de l'onduleur.}}"></i></sup>
												</span>
												<input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="inverter::lcd::cmd">
												<span class="input-group-btn">
													<a class="btn btn-default listCmdInfo roundedRight" data-type="inverter::lcd::cmd" data-subtype=""><i class="fas fa-list-alt"></i></a>
												</span>
											</div>
										</div>
									</div>
								</fieldset>
							</form>
						</div>
					</div>
					<!-- TEMP -->
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title"><i class="fas fas fa-bolt"></i> {{Températures}}</h3>
						</div>
						<div class="panel-body">
							<form class="form-horizontal">
								<fieldset>
									<!-- TEMP AC -->
									<div class="form-group">
										<div class="col-lg-1">
											<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="inverter::temp::ac::desactivate">{{Désactiver}}</label>
										</div>
										<div class="col-lg-4">
											<div class="input-group">
												<span class="input-group-addon roundedLeft" style="min-width: 125px;">
													{{AC}} <sup><i class="fas fa-question-circle" title="{{Sélectionner une commande info/numérique qui contient l'information de température AC.}}"></i></sup>
												</span>
												<input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="inverter::temp::ac::cmd" />
												<span class="input-group-btn">
													<a class="btn btn-default listCmdInfo roundedRight" data-type="inverter::temp::ac::cmd" data-subtype="numeric"><i class="fas fa-list-alt"></i></a>
												</span>
											</div>
										</div>
									</div>
									<!-- TEMP DC -->
									<div class="form-group">
										<div class="col-lg-1">
											<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="inverter::temp::dc::desactivate">{{Désactiver}}</label>
										</div>
										<div class="col-lg-4">
											<div class="input-group">
												<span class="input-group-addon roundedLeft" style="min-width: 125px;">
													{{DC}} <sup><i class="fas fa-question-circle" title="{{Sélectionner une commande info/numérique qui contient l'information de température DC.}}"></i></sup>
												</span>
												<input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="inverter::temp::dc::cmd" />
												<span class="input-group-btn">
													<a class="btn btn-default listCmdInfo roundedRight" data-type="inverter::temp::dc::cmd" data-subtype="numeric"><i class="fas fa-list-alt"></i></a>
												</span>
											</div>
										</div>
									</div>
								</fieldset>
							</form>
						</div>
					</div>
				</div>
			</div>
			<!-- 
			////////////////////////////
			//////////  GRID  //////////
			//////////////////////////// 
			-->
			<div role="tabpanel" class="tab-pane" id="configureGrid">
				<div class="tab-pane">
					<br>

					<!-- GRID CONFIGURATION -->
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title"><i class="fas fas fa-bolt"></i> {{Configuration}}</h3>
						</div>
						<div class="panel-body">
							<form class="form-horizontal">
								<fieldset>
									<div class="form-group">
										<div class="col-lg-3">
											<div class="input-group">
												<span class="input-group-addon roundedLeft" style="min-width: 125px;">{{Couleur}}</span>
												<input type="color" class="eqLogicAttr form-control" value="#5490c2" data-l1key="configuration" data-l2key="grid::color" />
												<span class="input-group-btn">
													<a class="btn btn-default restoreDefaut roundedRight" data-type="grid::color" data-defaut="#5490c2" title="{{Couleur par défaut}}"><i class="fas fa-eraser"></i></a>
												</span>
											</div>
										</div>
									</div>
								</fieldset>
							</form>
						</div>
					</div>

					<!-- GRID POWER -->
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title"><i class="fas fas fa-bolt"></i> {{Puissance instantanée}}</h3>
						</div>
						<div class="panel-body">
							<form class="form-horizontal">
								<fieldset>
									<div class="alert alert-info col-xs-12" style="text-align: center;margin-bottom: 15px;">
										{{Défaut}} : {{Commande de Puissance instantanée}} -> {{positive}} = {{consommation}} / {{négative}} = {{injection}}
									</div>
									<div class="form-group">
										<div class="col-lg-1">
											<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="grid::desactivate">{{Désactiver}}</label>
										</div>
										<div class="col-lg-4">
											<div class="input-group">
												<span class="input-group-addon roundedLeft" style="min-width: 125px;">{{Puissance}} </span>
												<input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="grid::power::cmd" />
												<span class="input-group-btn">
													<a class="btn btn-default listCmdInfo roundedRight" data-type="grid::power::cmd" data-subtype="numeric"><i class="fas fa-list-alt"></i></a>
												</span>
											</div>
										</div>
										<div class="col-lg-3">
											<div class="input-group">
												<span class="input-group-addon roundedLeft" style="min-width: 125px;">
													{{Max.}} <sup><i class="fas fa-question-circle" title="{{Puissance maximale du réseau.}}"></i></sup>
												</span>
												<input type="number" class="eqLogicAttr form-control roundedRight" data-l1key="configuration" data-l2key="grid::power::max">
											</div>
										</div>
										<div class="col-lg-1">
											<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="grid::invert">
												{{Inverser}} <sup><i class="fas fa-question-circle" title="{{positive}} = {{injection}} / {{négative}} = {{consommation}}"></i></sup>
											</label>
										</div>
									</div>
								</fieldset>
							</form>
						</div>
					</div>

					<!-- GRID ENERGY -->
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title"><i class="fas fas fa-bolt"></i> {{Energie quotidienne}}</h3>
						</div>
						<div class="panel-body">
							<form class="form-horizontal">
								<fieldset>
									<div id="div_grid_daily" class="col-xs-12">
										<div class="form-group">
											<div class="col-lg-1">
												<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="grid::daily::sell::desactivate">{{Désactiver}}</label>
											</div>
											<div class="col-lg-4">
												<div class="input-group">
													<span class="input-group-addon roundedLeft" style="min-width: 125px;">
														{{Energie vente}} <sup><i class="fas fa-question-circle" title="{{Injection}}"></i></sup>
													</span>
													<input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="grid::daily::sell::cmd" />
													<span class="input-group-btn">
														<a class="btn btn-default listCmdInfo roundedRight" data-type="grid::daily::sell::cmd" data-subtype="numeric"><i class="fas fa-list-alt"></i></a>
													</span>
												</div>
											</div>
											<div class="col-lg-3">
												<div class="input-group">
													<span class="input-group-addon roundedLeft" style="min-width: 125px;">{{Texte à afficher}}</span>
													<input class="eqLogicAttr form-control roundedRight" data-l1key="configuration" data-l2key="grid::daily::sell::txt" placeholder="{{VENTE JOUR}}" />
												</div>
											</div>
											<div class="col-lg-3">
												<div class="input-group">
													<span class="input-group-addon roundedLeft" style="min-width: 125px;">{{Couleur}}</span>
													<input type="color" class="eqLogicAttr form-control" value="#5490c2" data-l1key="configuration" data-l2key="grid::color::sell" />
													<span class="input-group-btn">
														<a class="btn btn-default restoreDefaut roundedRight" data-type="grid::color::sell" data-defaut="#5490c2" title="{{Couleur par défaut}}"><i class="fas fa-eraser"></i></a>
													</span>
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="col-lg-1">
												<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="grid::daily::buy::desactivate">{{Désactiver}}</label>
											</div>
											<div class="col-lg-4">
												<div class="input-group">
													<span class="input-group-addon roundedLeft" style="min-width: 125px;">
														{{Energie achat}} <sup><i class="fas fa-question-circle" title="{{Consommation}}"></i></sup>
													</span>
													<input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="grid::daily::buy::cmd" />
													<span class="input-group-btn">
														<a class="btn btn-default listCmdInfo roundedRight" data-type="grid::daily::buy::cmd" data-subtype="numeric"><i class="fas fa-list-alt"></i></a>
													</span>
												</div>
											</div>
											<div class="col-lg-3">
												<div class="input-group">
													<span class="input-group-addon roundedLeft" style="min-width: 125px;">{{Texte à afficher}}</span>
													<input class="eqLogicAttr form-control roundedRight" data-l1key="configuration" data-l2key="grid::daily::buy::txt" placeholder="{{ACHAT JOUR}}" />
												</div>
											</div>
											<div class="col-lg-3">
												<div class="input-group">
													<span class="input-group-addon roundedLeft" style="min-width: 125px;">{{Couleur}}</span>
													<input type="color" class="eqLogicAttr form-control" value="#5490c2" data-l1key="configuration" data-l2key="grid::color::buy" />
													<span class="input-group-btn">
														<a class="btn btn-default restoreDefaut roundedRight" data-type="grid::color::buy" data-defaut="#5490c2" title="{{Couleur par défaut}}"><i class="fas fa-eraser"></i></a>
													</span>
												</div>
											</div>
										</div>
									</div>
								</fieldset>
							</form>
						</div>
					</div>

					<!-- GRID POWER OUTAGE -->
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title"><i class="fas fas fa-bolt"></i> {{Panne de courant}}</h3>
						</div>
						<div class="panel-body">
							<form class="form-horizontal">
								<fieldset>
									<div id="div_grid" class="col-xs-12">
										<div class="form-group">
											<div class="col-lg-1">
												<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="grid::status::desactivate">{{Désactiver}}</label>
											</div>
											<div class="col-lg-4">
												<div class="input-group">
													<span class="input-group-addon roundedLeft" style="min-width: 125px;">
														{{Commande}} <sup><i class="fas fa-question-circle" title="{{Sélectionner une commande info/binaire qui contient l'information de présence secteur.}}"></i></sup>
													</span>
													<input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="grid::status::cmd" />
													<span class="input-group-btn">
														<a class="btn btn-default listCmdInfo roundedRight" data-type="grid::status::cmd" data-subtype="binary"><i class="fas fa-list-alt"></i></a>
													</span>
												</div>
											</div>
											<div class="col-lg-3">
												<div class="input-group">
													<span class="input-group-addon roundedLeft" style="min-width: 125px;">{{Couleur}}</span>
													<input type="color" class="eqLogicAttr form-control" value="#db041c" data-l1key="configuration" data-l2key="grid::color::nogrid" />
													<span class="input-group-btn">
														<a class="btn btn-default restoreDefaut roundedRight" data-type="grid::color::nogrid" data-defaut="#db041c" title="{{Couleur par défaut}}"><i class="fas fa-eraser"></i></a>
													</span>
												</div>
											</div>
										</div>
									</div>
								</fieldset>
							</form>
						</div>
					</div>

				</div>
			</div>
			<!-- 
			////////////////////////////
			/////////  SOLAR  //////////
			//////////////////////////// 
			-->
			<div role="tabpanel" class="tab-pane" id="configureSolar">
				<div class="tab-pane">
					<br>

					<!-- SOLAR CONFIGURATION -->
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title"><i class="fas fas fa-bolt"></i> Configuration</h3>
						</div>
						<div class="panel-body">
							<form class="form-horizontal">
								<fieldset>
									<div class="form-group">
										<div class="col-lg-3">
											<div class="input-group">
												<span class="input-group-addon roundedLeft" style="min-width: 125px;">{{Couleur}}</span>
												<input type="color" class="eqLogicAttr form-control" value="#ffa500" data-l1key="configuration" data-l2key="solar::color" />
												<span class="input-group-btn">
													<a class="btn btn-default restoreDefaut roundedRight" data-type="solar::color" data-defaut="#ffa500" title="{{Couleur par défaut}}"><i class="fas fa-eraser"></i></a>
												</span>
											</div>
										</div>
									</div>
								</fieldset>
							</form>
						</div>
					</div>

					<!-- SOLAR POWER -->
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title"><i class="fas fas fa-bolt"></i> {{Puissance totale instantanée}}</h3>
						</div>
						<div class="panel-body">
							<form class="form-horizontal">
								<fieldset>
									<div class="alert alert-info col-xs-12" style="text-align: center;margin-bottom: 15px;">
										{{Si vous disposez déjà d'une commande contenant la puissance totale des panneaux, vous pouvez renseigner celle-ci ci-dessous.}}
										<br>{{Le widget prendra en compte cette valeur à défaut de calculer la somme des puissances de chaque panneau.}}
									</div>
									<div class="form-group">
										<div class="col-lg-1">
											<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="solar::power::desactivate">{{Désactiver}}</label>
										</div>
										<div class="col-lg-4">
											<div class="input-group">
												<span class="input-group-addon roundedLeft" style="min-width: 125px;">
													{{Puissance totale}} <sup><i class="fas fa-question-circle" title="{{Laisser vide si vous voulez que le widget calcule la somme des panneaux.}}"></i></sup>
												</span>
												<input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="solar::power::cmd" />
												<span class="input-group-btn">
													<a class="btn btn-default listCmdInfo roundedRight" data-type="solar::power::cmd" data-subtype="numeric"><i class="fas fa-list-alt"></i></a>
												</span>
											</div>
										</div>
										<div class="col-lg-3">
											<div class="input-group">
												<span class="input-group-addon roundedLeft" style="min-width: 125px;">
													{{Max.}} <sup><i class="fas fa-question-circle" title="{{Puissance maximale que peuvent produire tous les panneaux réunis.}}"></i></sup>
												</span>
												<input type="number" class="eqLogicAttr form-control roundedRight" data-l1key="configuration" data-l2key="solar::power::max">
											</div>
										</div>
									</div>
								</fieldset>
							</form>
						</div>
					</div>

					<!-- ENERGY -->
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title"><i class="fas fas fa-bolt"></i> {{Energie quotidienne}}</h3>
						</div>
						<div class="panel-body">
							<form class="form-horizontal">
								<fieldset>
									<div class="form-group">
										<div class="col-lg-1">
											<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="solar::daily::desactivate">{{Désactiver}}</label>
										</div>
										<div class="col-lg-4">
											<div class="input-group">
												<span class="input-group-addon roundedLeft" style="min-width: 125px;">{{Energie}}</span>
												<input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="solar::daily::cmd" />
												<span class="input-group-btn">
													<a class="btn btn-default listCmdInfo roundedRight" data-type="solar::daily::cmd" data-subtype="numeric"><i class="fas fa-list-alt"></i></a>
												</span>
											</div>
										</div>
										<div class="col-lg-3">
											<div class="input-group">
												<span class="input-group-addon roundedLeft" style="min-width: 125px;">{{Texte à afficher}}</span>
												<input class="eqLogicAttr form-control roundedRight" data-l1key="configuration" data-l2key="solar::daily::txt" placeholder="{{PROD JOUR}}" />
											</div>
										</div>
									</div>
								</fieldset>
							</form>
						</div>
					</div>

					<!-- PVs -->
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title"><i class="fas fa-solar-panel"></i> {{Panneaux solaires}}</h3>
						</div>
						<div class="panel-body">
							<form class="form-horizontal">
								<fieldset>
									<div class="form-group">
										<div class="col-lg-3">
											<div class="input-group">
												<span class="input-group-addon roundedLeft" style="min-width: 125px;">{{Couleur si valeur = 0}}</span>
												<input type="color" class="eqLogicAttr form-control" value="#ffa500" data-l1key="configuration" data-l2key="solar::color::0" />
												<span class="input-group-btn">
													<a class="btn btn-default restoreDefaut roundedRight" data-type="solar::color::0" data-defaut="#ffa500" title="{{Couleur par défaut}}"><i class="fas fa-eraser"></i></a>
												</span>
											</div>
										</div>
										<div class="col-lg-3">
											<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="solar::color::hide">{{Masquer si valeur}} = 0 <sup><i class="fas fa-question-circle" title="{{Prioritaire sur la couleur}}"></i></sup></label>
										</div>
									</div>
								</fieldset>
							</form>
							<br>
							<form class="form-horizontal">
								<fieldset>
									<div class="alert alert-info col-xs-12" style="text-align: center;margin-bottom: 15px;">
										{{A partir de 7 panneaux solaires, l'affichage de ceux-ci sur le widget passe en horizontale au dessus des récepteurs.}}
									</div>
									<legend>
										<a class="btn btn-sm btn-success addPv" data-type="solar" data-subType="numeric"><i class="fas fa-plus-circle"></i> {{Ajout panneau solaire}}</a>
									</legend>
									<div id="div_pv" class="col-xs-12"></div>
								</fieldset>
							</form>
						</div>
					</div>

				</div>
			</div>
			<!-- 
			////////////////////////////
			///////  BATTERY  //////////
			//////////////////////////// 
			-->
			<div role="tabpanel" class="tab-pane" id="configureBattery">
				<div class="tab-pane">
					<br>

					<!-- BATTERY CONFIGURATION -->
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title"><i class="fas fas fa-bolt"></i> {{Configuration}}</h3>
						</div>
						<div class="panel-body">
							<form class="form-horizontal">
								<fieldset>
									<div class="form-group">
										<div class="col-lg-3">
											<div class="input-group">
												<span class="input-group-addon roundedLeft" style="min-width: 125px;">{{Couleur}}</span>
												<input type="color" class="eqLogicAttr form-control" value="#ffc0cb" data-l1key="configuration" data-l2key="battery::color" />
												<span class="input-group-btn">
													<a class="btn btn-default restoreDefaut roundedRight" data-type="battery::color" data-defaut="#ffc0cb" title="Couleur par défaut">
														<i class="fas fa-eraser"></i>
													</a>
												</span>
											</div>
										</div>
									</div>
								</fieldset>
							</form>
						</div>
					</div>

					<!-- BATTERY POWER -->
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title"><i class="fas fas fa-bolt"></i> {{Puissance instantanée}}</h3>
						</div>
						<div class="panel-body">
							<form class="form-horizontal">
								<fieldset>
									<div class="alert alert-info col-xs-12" style="text-align: center;margin-bottom: 15px;">
										{{Défaut}} : {{Commande de Puissance instantanée}} -> {{positive}} = {{décharge}} / {{négative}} = {{charge}}
									</div>
									<div class="form-group">
										<div class="col-lg-1">
											<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="battery::desactivate">{{Désactiver}}</label>
										</div>
										<div class="col-lg-4">
											<div class="input-group">
												<span class="input-group-addon roundedLeft" style="min-width: 125px;">{{Puissance}} </span>
												<input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="battery::power::cmd" />
												<span class="input-group-btn">
													<a class="btn btn-default listCmdInfo roundedRight" data-type="battery::power::cmd" data-subtype="numeric">
														<i class="fas fa-list-alt"></i>
													</a>
												</span>
											</div>
										</div>
										<div class="col-lg-3">
											<div class="input-group">
												<span class="input-group-addon roundedLeft" style="min-width: 125px;">
													{{Max.}} <sup><i class="fas fa-question-circle" title="{{Puissance maximale que peut produire la batterie.}}"></i></sup>
												</span>
												<input type="number" class="eqLogicAttr form-control roundedRight" data-l1key="configuration" data-l2key="battery::power::max">
											</div>
										</div>
										<div class="col-lg-3">
											<div class="input-group">
												<span class="input-group-addon roundedLeft" style="min-width: 125px;">
													{{Capacité}} <sup><i class="fas fa-question-circle" title="{{Capacité de la batterie.}}"></i></sup>
												</span>
												<input type="number" class="eqLogicAttr form-control roundedRight" data-l1key="configuration" data-l2key="battery::power::capacity">
											</div>
										</div>
										<div class="col-lg-1">
											<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="battery::power::invert">
												{{Inverser}} <sup><i class="fas fa-question-circle" title="{{positive}} = {{charge}} / {{négative}} = {{décharge}}"></i></sup>
											</label>
										</div>
									</div>
								</fieldset>
								<br>
								<fieldset>
									<legend><i class="fas fa-solar-panel"></i> {{Panneau solaire}} (mppt)</legend>
									<!-- BATTERY MPPT -->
									<div class="form-group">
										<div class="col-lg-1">
											<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="battery::mppt::desactivate">{{Désactiver}}</label>
										</div>
										<div class="col-lg-4">
											<div class="input-group">
												<span class="input-group-addon roundedLeft" style="min-width: 125px;">{{Puissance}} </span>
												<input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="battery::mppt::power::cmd" />
												<span class="input-group-btn">
													<a class="btn btn-default listCmdInfo roundedRight" data-type="battery::mppt::power::cmd" data-subtype="numeric"><i class="fas fa-list-alt"></i></a>
												</span>
											</div>
											<div class="input-group">
												<span class="input-group-addon roundedLeft" style="min-width: 125px;">{{Energie}} </span>
												<input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="battery::mppt::energy::cmd" />
												<span class="input-group-btn">
													<a class="btn btn-default listCmdInfo roundedRight" data-type="battery::mppt::energy::cmd" data-subtype="numeric"><i class="fas fa-list-alt"></i></a>
												</span>
											</div>
										</div>
										<div class="col-lg-3">
											<div class="input-group">
												<span class="input-group-addon roundedLeft" style="min-width: 125px;">
													{{Nom}} <sup><i class="fas fa-question-circle" title="{{Nom à afficher.}}"></i></sup>
												</span>
												<input class="eqLogicAttr form-control roundedRight" data-l1key="configuration" data-l2key="battery::mppt::name">
											</div>
										</div>
										<div class="col-lg-3">
											<div class="input-group">
												<span class="input-group-addon roundedLeft" style="min-width: 125px;">{{Couleur}}</span>
												<input type="color" class="eqLogicAttr form-control" value="#ffa500" data-l1key="configuration" data-l2key="battery::mppt::color" />
												<span class="input-group-btn">
													<a class="btn btn-default restoreDefaut roundedRight" data-type="battery::mppt::color" data-defaut="#ffa500" title="{{Couleur par défaut}}"><i class="fas fa-eraser"></i></a>
												</span>
											</div>
										</div>
									</div>
								</fieldset>
							</form>
						</div>
					</div>

					<!-- BATTERY ENERGY -->
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title"><i class="fas fas fa-bolt"></i> {{Energie quotidienne}}</h3>
						</div>
						<div class="panel-body">
							<form class="form-horizontal">
								<fieldset>
									<div class="form-group">
										<div class="col-lg-1">
											<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="battery::daily::charge::desactivate">{{Désactiver}}</label>
										</div>
										<div class="col-lg-4">
											<div class="input-group">
												<span class="input-group-addon roundedLeft" style="min-width: 150px;">{{Charge}} </span>
												<input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="battery::daily::charge::cmd" />
												<span class="input-group-btn">
													<a class="btn btn-default listCmdInfo roundedRight" data-type="battery::daily::charge::cmd" data-subtype="numeric"><i class="fas fa-list-alt"></i></a>
												</span>
											</div>
										</div>
										<div class="col-lg-3">
											<div class="input-group">
												<span class="input-group-addon roundedLeft" style="min-width: 125px;">{{Texte à afficher}}</span>
												<input class="eqLogicAttr form-control roundedRight" data-l1key="configuration" data-l2key="battery::daily::charge::txt" placeholder="{{CHARGE JOUR}}" />
											</div>
										</div>
										<div class="col-lg-3">
											<div class="input-group">
												<span class="input-group-addon roundedLeft" style="min-width: 150px;">{{Couleur}}</span>
												<input type="color" class="eqLogicAttr form-control" value="#ffc0cb" data-l1key="configuration" data-l2key="battery::color::charge" />
												<span class="input-group-btn">
													<a class="btn btn-default restoreDefaut roundedRight" data-type="battery::color::charge" data-defaut="#ffc0cb" title="{{Couleur par défaut}}"><i class="fas fa-eraser"></i></a>
												</span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<div class="col-lg-1">
											<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="battery::daily::discharge::desactivate">{{Désactiver}}</label>
										</div>
										<div class="col-lg-4">
											<div class="input-group">
												<span class="input-group-addon roundedLeft" style="min-width: 150px;">{{Décharge}} </span>
												<input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="battery::daily::discharge::cmd" />
												<span class="input-group-btn">
													<a class="btn btn-default listCmdInfo roundedRight" data-type="battery::daily::discharge::cmd" data-subtype="numeric"><i class="fas fa-list-alt"></i></a>
												</span>
											</div>
										</div>
										<div class="col-lg-3">
											<div class="input-group">
												<span class="input-group-addon roundedLeft" style="min-width: 125px;">{{Texte à afficher}}</span>
												<input class="eqLogicAttr form-control roundedRight" data-l1key="configuration" data-l2key="battery::daily::discharge::txt" placeholder="{{DECHARGE JOUR}}" />
											</div>
										</div>
										<div class="col-lg-3">
											<div class="input-group">
												<span class="input-group-addon roundedLeft" style="min-width: 150px;">{{Couleur}}</span>
												<input type="color" class="eqLogicAttr form-control" value="#ffc0cb" data-l1key="configuration" data-l2key="battery::color::discharge" />
												<span class="input-group-btn">
													<a class="btn btn-default restoreDefaut roundedRight" data-type="battery::color::discharge" data-defaut="#ffc0cb" title="{{Couleur par défaut}}"><i class="fas fa-eraser"></i></a>
												</span>
											</div>
										</div>
									</div>
								</fieldset>
							</form>
						</div>
					</div>

					<!-- BATTERY OTHER -->
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title"><i class="fas fas fa-bolt"></i> {{Autres}}</h3>
						</div>
						<div class="panel-body">
							<form class="form-horizontal">
								<fieldset>
									<div class="form-group">
										<div class="col-lg-1">
											<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="battery::voltage::desactivate">{{Désactiver}}</label>
										</div>
										<div class="col-lg-4">
											<div class="input-group">
												<span class="input-group-addon roundedLeft" style="min-width: 125px;">{{Tension}} </span>
												<input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="battery::voltage::cmd" />
												<span class="input-group-btn">
													<a class="btn btn-default listCmdInfo roundedRight" data-type="battery::voltage::cmd" data-subtype="numeric">
														<i class="fas fa-list-alt"></i>
													</a>
												</span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<div class="col-lg-1">
											<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="battery::current::desactivate">{{Désactiver}}</label>
										</div>
										<div class="col-lg-4">
											<div class="input-group">
												<span class="input-group-addon roundedLeft" style="min-width: 125px;">{{Intensité}} </span>
												<input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="battery::current::cmd" />
												<span class="input-group-btn">
													<a class="btn btn-default listCmdInfo roundedRight" data-type="battery::current::cmd" data-subtype="numeric">
														<i class="fas fa-list-alt"></i>
													</a>
												</span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<div class="col-lg-1">
											<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="battery::temp::desactivate">{{Désactiver}}</label>
										</div>
										<div class="col-lg-4">
											<div class="input-group">
												<span class="input-group-addon roundedLeft" style="min-width: 125px;">{{Température}} </span>
												<input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="battery::temp::cmd" />
												<span class="input-group-btn">
													<a class="btn btn-default listCmdInfo roundedRight" data-type="battery::temp::cmd" data-subtype="numeric">
														<i class="fas fa-list-alt"></i>
													</a>
												</span>
											</div>
										</div>
									</div>
								</fieldset>
							</form>
						</div>
					</div>

					<!-- BATTERY STATE -->
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title"><i class="fas fas fa-bolt"></i> {{État de charge}}</h3>
						</div>
						<div class="panel-body">
							<form class="form-horizontal">
								<fieldset>
									<!-- BATTERY SOC -->
									<div class="form-group">
										<div class="col-lg-1">
											<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="battery::soc::desactivate">{{Désactiver}}</label>
										</div>
										<div class="col-lg-4">
											<div class="input-group">
												<span class="input-group-addon roundedLeft" style="min-width: 125px;">
													{{État SOC}} <sup><i class="fas fa-question-circle" title="{{Sélectionner une commande info/numérique qui contient l'information de charge (%).}}"></i></sup>
												</span>
												<input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="battery::soc::cmd" />
												<span class="input-group-btn">
													<a class="btn btn-default listCmdInfo roundedRight" data-type="battery::soc::cmd" data-subtype="numeric"><i class="fas fa-list-alt"></i></a>
												</span>
											</div>
										</div>
										<div class="col-lg-3">
											<div class="input-group">
												<span class="input-group-addon roundedLeft" style="min-width: 125px;">
													{{SOC Min.}} <sup><i class="fas fa-question-circle" title="{{Pourcentage auquel la batterie passe à l'arrêt.}}"></i></sup>
												</span>
												<input type="number" class="eqLogicAttr form-control roundedRight" data-l1key="configuration" data-l2key="battery::soc::shutdown">
											</div>
										</div>
										<div class="col-lg-4">
											<div class="input-group">
												<span class="input-group-addon roundedLeft input-group-addon" style="min-width: 125px;">
													{{Icône}}
												</span>
												<input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="battery::img" disabled />
												<span class="input-group-btn">
													<a class="btn btn-default bt_library" title="{{Bibliothèque}}" data-type="battery::img"><i class="fas fa-photo-video"></i></a>
												</span>
												<span class="input-group-btn">
													<a class="btn btn-default restoreDefaut roundedRight" data-type="battery::img" data-defaut="" title="{{Icône par défaut}}">
														<i class="fas fa-eraser"></i>
													</a>
												</span>
											</div>
										</div>
									</div>
									<br>
									<!-- BATTERY STATE COLOR -->
									<div class="alert alert-info col-xs-12" style="text-align: center;margin-bottom: 15px;">
										{{Couleur personnalisée de l'icône batterie en fonction de la charge restante.}}<br>
										<i class="fas fa-exclamation-triangle"></i> {{Attention : cette fonctionnalité sera désactivée si vous utilisez une icône perso ou une icône intégrée.}}
									</div>
									<div class="form-group">
										<div class="col-lg-1">
											<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="battery::color::state::desactivate">{{Désactiver}}</label>
										</div>
									</div>
									<div class="form-group">
										<div class="col-lg-4">
											<div class="input-group">
												<span class="input-group-addon roundedLeft" style="min-width: 125px;">% = 0</span>
												<input type="color" class="eqLogicAttr form-control" value="#ff0000" data-l1key="configuration" data-l2key="battery::color::state::0" />
												<span class="input-group-btn">
													<a class="btn btn-default restoreDefaut roundedRight" data-type="battery::color::state::0" data-defaut="#ff0000" title="{{Couleur par défaut}}"><i class="fas fa-eraser"></i></a>
												</span>
											</div>
											<div class="input-group">
												<span class="input-group-addon roundedLeft" style="min-width: 125px;">0 < % < 50</span>
														<input type="color" class="eqLogicAttr form-control" value="#ff4005" data-l1key="configuration" data-l2key="battery::color::state::25" />
														<span class="input-group-btn">
															<a class="btn btn-default restoreDefaut roundedRight" data-type="battery::color::state::25" data-defaut="#ff4005" title="{{Couleur par défaut}}"><i class="fas fa-eraser"></i></a>
														</span>
											</div>
											<div class="input-group">
												<span class="input-group-addon roundedLeft" style="min-width: 125px;">50 <= % < 75</span>
														<input type="color" class="eqLogicAttr form-control" value="#ffa500" data-l1key="configuration" data-l2key="battery::color::state::50" />
														<span class="input-group-btn">
															<a class="btn btn-default restoreDefaut roundedRight" data-type="battery::color::state::50" data-defaut="#ffa500" title="{{Couleur par défaut}}"><i class="fas fa-eraser"></i></a>
														</span>
											</div>
											<div class="input-group">
												<span class="input-group-addon roundedLeft" style="min-width: 125px;">75 <= % < 100</span>
														<input type="color" class="eqLogicAttr form-control" value="#9ACD32" data-l1key="configuration" data-l2key="battery::color::state::75" />
														<span class="input-group-btn">
															<a class="btn btn-default restoreDefaut roundedRight" data-type="battery::color::state::75" data-defaut="#9ACD32" title="{{Couleur par défaut}}"><i class="fas fa-eraser"></i></a>
														</span>
											</div>
											<div class="input-group">
												<span class="input-group-addon roundedLeft" style="min-width: 125px;">% = 100</span>
												<input type="color" class="eqLogicAttr form-control" value="#008000" data-l1key="configuration" data-l2key="battery::color::state::100" />
												<span class="input-group-btn">
													<a class="btn btn-default restoreDefaut roundedRight" data-type="battery::color::state::100" data-defaut="#008000" title="{{Couleur par défaut}}"><i class="fas fa-eraser"></i></a>
												</span>
											</div>
										</div>
									</div>
								</fieldset>
							</form>
						</div>
					</div>

				</div>
			</div>
			<!-- 
			////////////////////////////
			//////////  LOAD  //////////
			//////////////////////////// 
			-->
			<div role="tabpanel" class="tab-pane" id="configureLoad">
				<div class="tab-pane">
					<br>

					<!-- LOAD CONFIGURATION -->
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title"><i class="fas fas fa-bolt"></i> {{Configuration}}</h3>
						</div>
						<div class="panel-body">
							<form class="form-horizontal">
								<fieldset>
									<div class="form-group">
										<div class="col-lg-3">
											<div class="input-group">
												<span class="input-group-addon roundedLeft" style="min-width: 125px;">{{Couleur}}</span>
												<input type="color" class="eqLogicAttr form-control" value="#5fb6ad" data-l1key="configuration" data-l2key="load::color">
												<span class="input-group-btn">
													<a class="btn btn-default restoreDefaut roundedRight" data-type="load::color" data-defaut="#5fb6ad" title="{{Couleur par défaut}}"><i class="fas fa-eraser"></i></a>
												</span>
											</div>
										</div>
									</div>
								</fieldset>
							</form>
						</div>
					</div>

					<!-- LOAD POWER -->
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title"><i class="fas fas fa-bolt"></i> {{Puissance totale instantanée}}</h3>
						</div>
						<div class="panel-body">
							<form class="form-horizontal">
								<fieldset>
									<div class="alert alert-info col-xs-12" style="text-align: center;margin-bottom: 15px;">
										{{Si vous disposez déjà d'une commande contenant la puissance totale des récepteurs, vous pouvez renseigner celle-ci ci-dessous.}}
										<br>{{Le widget prendra en compte cette valeur à défaut de calculer la somme des puissances de chaque récepteur.}}
									</div>
									<div class="form-group">
										<div class="col-lg-1">
											<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="load::power::desactivate">{{Désactiver}}</label>
										</div>
										<div class="col-lg-4">
											<div class="input-group">
												<span class="input-group-addon roundedLeft" style="min-width: 125px;">
													{{Puissance totale}} <sup><i class="fas fa-question-circle" title="{{Laisser vide si vous voulez que le widget calcule la somme des récepteurs.}}"></i></sup>
												</span>
												<input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="load::power::cmd">
												<span class="input-group-btn">
													<a class="btn btn-default listCmdInfo roundedRight" data-type="load::power::cmd" data-subtype="numeric"><i class="fas fa-list-alt"></i></a>
												</span>
											</div>
										</div>
										<div class="col-lg-3">
											<div class="input-group">
												<span class="input-group-addon roundedLeft" style="min-width: 125px;">{{Max.}} <sup><i class="fas fa-question-circle" title="{{Puissance maximale de consommation.}}"></i></sup></span>
												<input type="number" class="eqLogicAttr form-control roundedRight" data-l1key="configuration" data-l2key="load::power::max">
											</div>
										</div>
									</div>
								</fieldset>
							</form>
						</div>
					</div>

					<!-- ENERGY -->
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title"><i class="fas fas fa-bolt"></i> {{Energie quotidienne}}</h3>
						</div>
						<div class="panel-body">
							<form class="form-horizontal">
								<fieldset>
									<div class="form-group">
										<div class="col-lg-1">
											<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="load::daily::desactivate">{{Désactiver}}</label>
										</div>
										<div class="col-lg-4">
											<div class="input-group">
												<span class="input-group-addon roundedLeft" style="min-width: 125px;">{{Energie}} </span>
												<input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="load::daily::cmd" />
												<span class="input-group-btn">
													<a class="btn btn-default listCmdInfo roundedRight" data-type="load::daily::cmd" data-subtype="numeric"><i class="fas fa-list-alt"></i></a>
												</span>
											</div>
										</div>
										<div class="col-lg-3">
											<div class="input-group">
												<span class="input-group-addon roundedLeft" style="min-width: 125px;">{{Texte à afficher}}</span>
												<input class="eqLogicAttr form-control roundedRight" data-l1key="configuration" data-l2key="load::daily::txt" placeholder="{{CONSO JOUR}}" />
											</div>
										</div>
									</div>
								</fieldset>
							</form>
						</div>
					</div>

					<!-- Loads -->
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title"><i class="fas fa-solar-panel"></i> {{Récepteurs}}</h3>
						</div>
						<div class="panel-body">
							<form class="form-horizontal">
								<fieldset>
									<div class="form-group">
										<div class="col-lg-3">
											<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="load::animate::disable">{{Désactiver les animations}} </label>
										</div>
									</div>
								</fieldset>
							</form>
							<form class="form-horizontal">
								<fieldset>
									<div class="form-group">
										<div class="col-lg-3">
											<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="load::force4load">{{Forcer 4 recépteurs par colonne}} </label>
										</div>
									</div>
								</fieldset>
							</form>
							<br>
							<form class="form-horizontal">
								<fieldset>
									<legend>
										<a class="btn btn-sm btn-success addLoad" data-type="load" data-subType="numeric"><i class="fas fa-plus-circle"></i> {{Ajout récepteur}}</a>
									</legend>
									<div id="div_load" class="col-xs-12"></div>
								</fieldset>
							</form>
						</div>
					</div>

				</div>
			</div>
			<!-- 
			////////////////////////////
			///////  GENERATOR  ////////
			//////////////////////////// 
			-->
			<div role="tabpanel" class="tab-pane" id="configureAux">
				<div class="tab-pane">
					<br>

					<!-- GENERATOR CONFIGURATION -->
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title"><i class="fas fas fa-bolt"></i> {{Configuration}}</h3>
						</div>
						<div class="panel-body">
							<form class="form-horizontal">
								<fieldset>
									<div class="form-group">
										<div class="col-lg-3">
											<div class="input-group">
												<span class="input-group-addon roundedLeft" style="min-width: 125px;">{{Couleur}}</span>
												<input type="color" class="eqLogicAttr form-control" value="#a43df5" data-l1key="configuration" data-l2key="aux::color" />
												<span class="input-group-btn">
													<a class="btn btn-default restoreDefaut roundedRight" data-type="aux::color" data-defaut="#a43df5" title="{{Couleur par défaut}}"><i class="fas fa-eraser"></i></a>
												</span>
											</div>
										</div>
									</div>
								</fieldset>
							</form>
						</div>
					</div>

					<!-- GENERATOR POWER -->
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title"><i class="fas fas fa-bolt"></i> {{Puissance instantanée}}</h3>
						</div>
						<div class="panel-body">
							<form class="form-horizontal">
								<fieldset>
									<div class="form-group">
										<div class="col-lg-1">
											<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="aux::desactivate">{{Désactiver}}</label>
										</div>
										<div class="col-lg-4">
											<div class="input-group">
												<span class="input-group-addon roundedLeft" style="min-width: 125px;">{{Puissance}} </span>
												<input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="aux::power::cmd">
												<span class="input-group-btn">
													<a class="btn btn-default listCmdInfo roundedRight" data-type="aux::power::cmd" data-subtype="numeric"><i class="fas fa-list-alt"></i></a>
												</span>
											</div>
										</div>
										<div class="col-lg-3">
											<div class="input-group">
												<span class="input-group-addon roundedLeft" style="min-width: 125px;">
													{{Max.}} <sup><i class="fas fa-question-circle" title="{{Puissance maximale du générateur.}}"></i></sup>
												</span>
												<input type="number" class="eqLogicAttr form-control roundedRight" data-l1key="configuration" data-l2key="aux::power::max">
											</div>
										</div>
									</div>
								</fieldset>
							</form>
						</div>
					</div>

					<!-- ENERGY -->
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title"><i class="fas fas fa-bolt"></i> {{Energie quotidienne}}</h3>
						</div>
						<div class="panel-body">
							<form class="form-horizontal">
								<fieldset>
									<div class="form-group">
										<div class="col-lg-1">
											<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="aux::daily::desactivate">{{Désactiver}}</label>
										</div>
										<div class="col-lg-4">
											<div class="input-group">
												<span class="input-group-addon roundedLeft" style="min-width: 125px;">{{Energie}} </span>
												<input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="aux::daily::cmd" />
												<span class="input-group-btn">
													<a class="btn btn-default listCmdInfo roundedRight" data-type="aux::daily::cmd" data-subtype="numeric"><i class="fas fa-list-alt"></i></a>
												</span>
											</div>
										</div>
										<div class="col-lg-3">
											<div class="input-group">
												<span class="input-group-addon roundedLeft" style="min-width: 125px;">{{Texte à afficher}}</span>
												<input class="eqLogicAttr form-control roundedRight" data-l1key="configuration" data-l2key="load::aux::txt" placeholder="{{PROD AUX}}" />
											</div>
										</div>
									</div>
								</fieldset>
							</form>
						</div>
					</div>

				</div>
			</div>
			<!-- 
			////////////////////////////
			/////////  PERSO  //////////
			//////////////////////////// 
			-->
			<div role="tabpanel" class="tab-pane" id="configurePerso">
				<div class="tab-pane">
					<br>

					<!-- Perso CONFIGURATION -->
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title"><i class="fas fas fa-bolt"></i> {{Perso}}</h3>
						</div>
						<div class="panel-body">
							<form class="form-horizontal">
								<fieldset>
									<legend>
										<a class="btn btn-sm btn-success addPerso" data-type="perso" data-subType="numeric"><i class="fas fa-plus-circle"></i> {{Ajout perso}}</a>
									</legend>
									<div id="div_perso" class="col-xs-12"></div>
								</fieldset>
							</form>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>
<style>
	.panel-body {
		padding-bottom: 15px;
	}
</style>
<?php include_file('desktop', 'powerFlow', 'js', 'powerFlow'); ?>
<?php include_file('core', 'plugin.template', 'js'); ?>