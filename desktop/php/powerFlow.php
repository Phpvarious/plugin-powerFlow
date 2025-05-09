<?php
ini_set('display_errors', 0);
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
				<!-- </a><a class="btn btn-default btn-sm eqLogicAction" data-action="copy"><i class="fas fa-copy"></i><span class="hidden-xs"> {{Dupliquer}}</span>-->
				</a><a class="btn btn-sm btn-success eqLogicAction" data-action="save"><i class="fas fa-check-circle"></i> {{Sauvegarder}}
				</a><a class="btn btn-sm btn-danger eqLogicAction roundedRight" data-action="remove"><i class="fas fa-minus-circle"></i> {{Supprimer}}
				</a>
			</span>
		</div>
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation"><a class="eqLogicAction cursor" aria-controls="home" role="tab" data-action="returnToThumbnailDisplay"><i class="fas fa-arrow-circle-left"></i></a></li>
			<li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-tachometer-alt"></i><span class="hidden-xs"> {{Équipement}}</span></a></li>
            <li role="presentation"><a href="#configureGrid" data-toggle="tab"><i class="mdi-transmission-tower"></i><span class="hidden-xs"> {{Réseau}}</span></a></li>
            <li role="presentation"><a href="#configureSolar" data-toggle="tab"><i class="fas fa-solar-panel"></i><span class="hidden-xs"> {{Solaire}}</span></a></li>
            <li role="presentation"><a href="#configureBattery" data-toggle="tab"><i class="fas fa-battery-half"></i><span class="hidden-xs"> {{Batterie}}</span></a></li>
            <li role="presentation"><a href="#configureLoad" data-toggle="tab"><i class="fas fa-battery-half"></i><span class="hidden-xs"> {{Load}}</span></a></li>
            <li role="presentation"><a href="#configureInverter" data-toggle="tab"><i class="icon mdi-application-cog"></i><span class="hidden-xs"> {{Onduleur}}</span></a></li>
			<!--<li role="presentation"><a href="#commandtab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fas fa-list"></i> {{Commandes}}</a></li>-->
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
									<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked>{{Activer}}</label>
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
								<label class="col-sm-4 control-label">
									{{Gauges}} <sup><i class="fas fa-question-circle" title="{{Désactiver les gauges}}"></i></sup></label>
								<div class="col-sm-6">
									<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="disableGauge">{{Désactiver}}</label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">
									{{Formatage milliers}} <sup><i class="fas fa-question-circle" title="{{Activer le formattage des milliers}} ({{ex :}} 5000 -> 5 000)"></i></sup></label>
								<div class="col-sm-6">
									<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="formatMillier">{{Activer}}</label>
								</div>
							</div>
						</div>
						<div class="col-lg-6">
						</div>
					</fieldset>
				</form>
			</div>
			<!--
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
			-->
			<!-- 
			////////////////////////////
			//////////  GRID  //////////
			//////////////////////////// 
			-->
			<div class="tab-pane" id="configureGrid">
				<br>
				
				<!-- GRID CONFIGURATION -->
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title"><i class="fas fas fa-bolt"></i> {{Configuration}}</h3>
					</div>
					<div class="panel-body">
						<form class="form-horizontal">
							<fieldset>
								<div class="alert alert-warning col-xs-12" style="text-align: center;margin-bottom: 15px;">
									{{Commande de Puissance instantanée. (positive = consommation / négative = injection)}}
								</div>
								<div class="form-group">
									<div class="col-lg-1">
										<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="grid::activate">{{Activer}}</label>
									</div>
									<div class="col-lg-4">
										<div class="input-group">
											<span class="input-group-addon roundedLeft" style="min-width: 110px;">{{Puissance}} <sup><i class="fas fa-question-circle" title="{{Selectionner une commande info/numerique qui contient l'information de puissance du réseau.}}"></i></sup></span>
											<input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="grid::power::cmd" />
											<span class="input-group-btn">
												<a class="btn btn-default listCmdInfo roundedRight" data-type="grid::power::cmd" data-subtype="numeric"><i class="fas fa-list-alt"></i></a>
											</span>
										</div>
									</div>
									<div class="col-lg-2">
										<div class="input-group">
											<span class="input-group-addon roundedLeft" style="min-width: 110px;">
												Maxi <sup><i class="fas fa-question-circle" title="{{Puissance maximale du réseau.}}"></i></sup>
											</span>
											<input type="number" class="eqLogicAttr form-control roundedRight" data-l1key="configuration" data-l2key="grid::power::max">
										</div>
									</div>
									<div class="col-lg-2">
										<div class="input-group">
											<span class="input-group-addon roundedLeft" style="min-width: 110px;">{{Couleur}}</span>
											<input type="color" class="eqLogicAttr form-control" value="#5490c2" data-l1key="configuration" data-l2key="grid::color" />
											<span class="input-group-btn">
												<a class="btn btn-default restoreDefaut roundedRight" data-type="grid::color" data-defaut="#5490c2" title="{{Couleur par défaut}}"><i class="fas fa-eraser"></i></a>
											</span>
										</div>
									</div>
								</div>
								<div class="form-group">
									<div class="col-lg-2 col-xs-offset-1">
										<div class="input-group">
											<span class="input-group-addon roundedLeft" style="min-width: 110px;">{{Vente}}</span>
											<input type="color" class="eqLogicAttr form-control" value="#5490c2" data-l1key="configuration" data-l2key="grid::color::sell" />
											<span class="input-group-btn">
												<a class="btn btn-default restoreDefaut roundedRight" data-type="grid::color::sell" data-defaut="#5490c2" title="{{Couleur par défaut}}"><i class="fas fa-eraser"></i></a>
											</span>
										</div>
									</div>
									<div class="col-lg-2">
										<div class="input-group">
											<span class="input-group-addon roundedLeft" style="min-width: 110px;">{{Achat}}</span>
											<input type="color" class="eqLogicAttr form-control" value="#5490c2" data-l1key="configuration" data-l2key="grid::color::buy" />
											<span class="input-group-btn">
												<a class="btn btn-default restoreDefaut roundedRight" data-type="grid::color::buy" data-defaut="#5490c2" title="{{Couleur par défaut}}"><i class="fas fa-eraser"></i></a>
											</span>
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
								<div class="alert alert-info col-xs-12" style="text-align: center;margin-bottom: 15px;">
									<i class="fas fa-exclamation"></i> {{Les energies quotidiennes seront non visible sur le widget si la commande puissance n'a pas été renseignée ou si elle est désactivée.}}
								</div>
								<div id="div_grid_daily" class="col-xs-12">
									<div class="form-group">
										<div class="col-lg-1">
											<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="grid::daily::sell::activate">{{Activer}}</label>
										</div>
										<div class="col-lg-5">
											<div class="input-group">
												<span class="input-group-addon roundedLeft" style="min-width: 110px;">{{Energie vente}} <sup><i class="fas fa-question-circle" title="{{Selectionner une commande info/numerique qui contient l'information d'énergie vendue. (injection)}}"></i></sup></span>
												<input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="grid::daily::sell::cmd"/>
												<span class="input-group-btn">
													<a class="btn btn-default listCmdInfo roundedRight" data-type="grid::daily::sell::cmd" data-subtype="numeric"><i class="fas fa-list-alt"></i></a>
												</span>
											</div>
										</div>
										<div class="col-lg-3">
											<div class="input-group">
												<span class="input-group-addon roundedLeft" style="min-width: 110px;">{{Texte à afficher}}</span>
												<input class="eqLogicAttr form-control roundedRight" data-l1key="configuration" data-l2key="grid::daily::sell::txt" placeholder="{{VENTE JOUR}}" />
											</div>
										</div>
									</div>
                                                  
									<div class="form-group">
										<div class="col-lg-1">
											<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="grid::daily::buy::activate">{{Activer}}</label>
										</div>
										<div class="col-lg-5">
											<div class="input-group">
												<span class="input-group-addon roundedLeft" style="min-width: 110px;">{{Energie achat}} <sup><i class="fas fa-question-circle" title="{{Selectionner une commande info/numerique qui contient l'information d'énergie achetée. (consommation)}}"></i></sup></span>
												<input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="grid::daily::buy::cmd"/>
												<span class="input-group-btn">
													<a class="btn btn-default listCmdInfo roundedRight" data-type="grid::daily::buy::cmd" data-subtype="numeric"><i class="fas fa-list-alt"></i></a>
												</span>
											</div>
										</div>
										<div class="col-lg-3">
											<div class="input-group">
												<span class="input-group-addon roundedLeft" style="min-width: 110px;">{{Texte à afficher}}</span>
												<input class="eqLogicAttr form-control roundedRight" data-l1key="configuration" data-l2key="grid::daily::buy::txt" placeholder="{{ACHAT JOUR}}" />
											</div>
										</div>
									</div>
								</div>
							</fieldset>
						</form>
					</div>
				</div>
				
				<!-- POWER OUTAGE -->
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
											<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="grid::status::activate">{{Activer}}</label>
										</div>
										<div class="col-lg-4">
											<div class="input-group">
												<span class="input-group-addon roundedLeft" style="min-width: 110px;">
                                                  {{Commande}} <sup><i class="fas fa-question-circle" title="{{Selectionner une commande info/binaire qui contient l'information de présence secteur.}}"></i></sup>
                                                </span>
												<input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="grid::status::cmd" />
												<span class="input-group-btn">
													<a class="btn btn-default listCmdInfo roundedRight" data-type="grid::status::cmd" data-subtype="binary"><i class="fas fa-list-alt"></i></a>
												</span>
											</div>
										</div>
										<div class="col-lg-2">
											<div class="input-group">
                                                <span class="input-group-addon roundedLeft" style="min-width: 110px;">{{Couleur}}</span>
												<input type="color" class="eqLogicAttr form-control"  value="#db041c" data-l1key="configuration" data-l2key="grid::color::nogrid" />
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
			<!-- 
			////////////////////////////
			/////////  SOLAR  //////////
			//////////////////////////// 
			-->
			<div class="tab-pane" id="configureSolar">
				<br>
				<!-- CONFIGURATION -->
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title"><i class="fas fas fa-bolt"></i> Configuration</h3>
					</div>
					<div class="panel-body">
						<form class="form-horizontal">
							<fieldset>
								<div class="alert alert-info col-xs-12" style="text-align: center;margin-bottom: 15px;">
									{{Si vous disposez déja d'une commande contenant la puissance totale des panneaux, vous pouvez renseigner celle-ci ci-dessous.}}
									<br>{{Le widget prendra en compte cette valeur a défaut de calculer la somme des puissances de chaques panneaux.}}
								</div>
								<div class="form-group">
									<div class="col-lg-1">
										<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="solar::power::activate">{{Activer}}</label>
									</div>
									<div class="col-lg-4">
										<div class="input-group">
											<span class="input-group-addon roundedLeft" style="min-width: 110px;">{{Puissance totale}} <sup><i class="fas fa-question-circle" title="{{Selectionner une commande info/numerique qui contient l'information de puissance totale.}}<br>{{Laisser vide si vous voulez que le widget calcul la somme des panneaux.}}"></i></sup></span>
											<input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="solar::power::cmd" />
											<span class="input-group-btn">
												<a class="btn btn-default listCmdInfo roundedRight" data-type="solar::power::cmd" data-subtype="numeric"><i class="fas fa-list-alt"></i></a>
											</span>
										</div>
									</div>
								</div>
								<hr class="hrPrimary">
								<div class="form-group">
									<div class="col-lg-2 col-xs-offset-1">
										<div class="input-group">
											<span class="input-group-addon roundedLeft" style="min-width: 110px;">{{Couleur}}</span>
											<input type="color" class="eqLogicAttr form-control" value="#ffa500" data-l1key="configuration" data-l2key="solar::color" />
											<span class="input-group-btn">
												<a class="btn btn-default restoreDefaut roundedRight" data-type="solar::color" data-defaut="#ffa500" title="{{Couleur par défaut}}"><i class="fas fa-eraser"></i></a>
											</span>
										</div>
									</div>
									<div class="col-lg-2">
										<div class="input-group">
											<span class="input-group-addon roundedLeft" style="min-width: 110px;">{{Couleur si valeur = 0}}</span>
											<input type="color" class="eqLogicAttr form-control" value="#ffa500" data-l1key="configuration" data-l2key="solar::color::0" />
											<span class="input-group-btn">
												<a class="btn btn-default restoreDefaut roundedRight" data-type="solar::color::0" data-defaut="#ffa500" title="{{Couleur par défaut}}"><i class="fas fa-eraser"></i></a>
											</span>
										</div>
									</div>
									<div class="col-lg-2">
										<div class="input-group">
											<span class="input-group-addon roundedLeft" style="min-width: 110px;">Max. <sup><i class="fas fa-question-circle" title="{{Puissance maximale que peut produire tous les panneaux réunis.}}"></i></sup></span>
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
										<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="solar::daily::activate">{{Activer}}</label>
									</div>
									<div class="col-lg-4">
										<div class="input-group">
											<span class="input-group-addon roundedLeft" style="min-width: 110px;">{{Commande}} <sup><i class="fas fa-question-circle" title="{{Selectionner une commande info/numerique qui contient l'information d'énergie.}}"></i></sup></span>
											<input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="solar::daily::cmd"/>
											<span class="input-group-btn">
												<a class="btn btn-default listCmdInfo roundedRight" data-type="solar::daily::cmd" data-subtype="numeric"><i class="fas fa-list-alt"></i></a>
											</span>
										</div>
									</div>
									<div class="col-lg-2">
										<div class="input-group">
											<span class="input-group-addon roundedLeft" style="min-width: 110px;">{{Texte à afficher}}</span>
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
								<legend>
									<a class="btn btn-sm btn-success addPv" data-type="solar" data-subType="numeric"><i class="fas fa-plus-circle"></i> {{Ajouter un panneau solaire}}</a>
								</legend>
								<div id="div_pv" class="col-xs-12"></div>
							</fieldset>
						</form>
					</div>
				</div>
			</div>
			<!-- 
			////////////////////////////
			///////  BATTERY  //////////
			//////////////////////////// 
			-->
			<div class="tab-pane" id="configureBattery">
				<br>

				<!-- //////  CONFIGURATION  ////// -->
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title"><i class="fas fas fa-bolt"></i> {{Configuration}}</h3>
					</div>
					<div class="panel-body">
						<form class="form-horizontal">
							<fieldset>
								<div class="alert alert-warning col-xs-12" style="text-align: center;margin-bottom: 15px;">
									{{Commande de Puissance instantanée. (positive = décharge / négative = charge)}}
									<br>
									<i class="fas fa-exclamation-triangle"></i> {{Commande obligatoire pour afficher les éléments de catégorie batterie.}}
								</div>
								<div class="form-group">
									<div class="col-lg-1">
										<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="battery::activate">{{Activer}}</label>
									</div>
									<div class="col-lg-4">
										<div class="input-group">
											<span class="input-group-addon roundedLeft" style="min-width: 110px;">
												{{Puissance}} <sup><i class="fas fa-question-circle" title="{{Selectionner une commande info/numerique qui contient l'information de puissance de la batterie.}}"></i></sup>
											</span>
											<input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="battery::power::cmd" />
											<span class="input-group-btn">
												<a class="btn btn-default listCmdInfo roundedRight" data-type="battery::power::cmd" data-subtype="numeric">
													<i class="fas fa-list-alt"></i>
												</a>
											</span>
										</div>
									</div>
									<div class="col-lg-4">
										<div class="input-group">
											<span class="input-group-addon roundedLeft" style="min-width: 110px;">
												{{Max.}} <sup><i class="fas fa-question-circle" title="{{Puissance maximale que peut produire la batterie.}}"></i></sup>
											</span>
											<input type="number" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="battery::power::max">
											<span class="input-group-addon" style="min-width: 110px;">
												{{Capacité}} <sup><i class="fas fa-question-circle" title="{{Capacité de la batterie.}}"></i></sup>
											</span>
											<input type="number" class="eqLogicAttr form-control roundedRight" data-l1key="configuration" data-l2key="battery::power::capacity">
										</div>
									</div>
									<div class="col-lg-2">
										<div class="input-group">
											<span class="input-group-addon roundedLeft" style="min-width: 110px;">{{Couleur}}</span>
											<input type="color" class="eqLogicAttr form-control" value="#ffc0cb" data-l1key="configuration" data-l2key="battery::color" />
											<span class="input-group-btn">
												<a class="btn btn-default restoreDefaut roundedRight" data-type="battery::color" data-defaut="#ffc0cb" title="Couleur par défaut">
													<i class="fas fa-eraser"></i>
												</a>
											</span>
										</div>
									</div>
								</div>
								<hr class="hrPrimary">
								<div class="form-group">
									<div class="col-lg-1">
										<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="battery::voltage::activate">{{Activer}}</label>
									</div>
									<div class="col-lg-4">
										<div class="input-group">
											<span class="input-group-addon roundedLeft" style="min-width: 110px;">
												{{Tension}} <sup><i class="fas fa-question-circle" title="{{Selectionner une commande info/numerique qui contient l'information de tension de la batterie.}}"></i></sup>
											</span>
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
										<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="battery::current::activate">{{Activer}}</label>
									</div>
									<div class="col-lg-4">
										<div class="input-group">
											<span class="input-group-addon roundedLeft" style="min-width: 110px;">
												{{Intensité}} <sup><i class="fas fa-question-circle" title="{{Selectionner une commande info/numerique qui contient l'information d'intensité de la batterie.}}"></i></sup>
											</span>
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
										<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="battery::temp::activate">{{Activer}}</label>
									</div>
									<div class="col-lg-4">
										<div class="input-group">
											<span class="input-group-addon roundedLeft" style="min-width: 110px;">
												{{Température}} <sup><i class="fas fa-question-circle" title="{{Selectionner une commande info/numerique qui contient l'information de température de la batterie.}}"></i></sup>
											</span>
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

                <!-- //////  BATTERY  ////// -->
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title"><i class="fas fas fa-bolt"></i> {{État de charge}}</h3>
					</div>
					<div class="panel-body">
						<form class="form-horizontal">
							<fieldset>
								
								<!-- SOC -->
								<div class="form-group">
									<div class="col-lg-1">
										<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="battery::soc::activate">{{Activer}}</label>
									</div>
									<div class="col-lg-4">
										<div class="input-group">
											<span class="input-group-addon roundedLeft" style="min-width: 110px;">
												{{État SOC}} <sup><i class="fas fa-question-circle" title="{{Selectionner une commande info/numerique qui contient l'information de charge (%).}}"></i></sup>
											</span>
											<input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="battery::soc::cmd" />
											<span class="input-group-btn">
												<a class="btn btn-default listCmdInfo roundedRight" data-type="battery::soc::cmd" data-subtype="numeric"><i class="fas fa-list-alt"></i></a>
											</span>
										</div>
									</div>
									<div class="col-lg-2">
										<div class="input-group">
											<span class="input-group-addon roundedLeft" style="min-width: 110px;">
												{{SOC Min.}} <sup><i class="fas fa-question-circle" title="{{Pourcentage a laquelle la batterie passe à l'arrêt.}}"></i></sup>
											</span>
											<input type="number" class="eqLogicAttr form-control roundedRight" data-l1key="configuration" data-l2key="battery::soc::shutdown">
										</div>
									</div>
                                    <div class="col-lg-3">
										<div class="input-group">
											<span class="input-group-addon roundedLeft input-group-addon" style="min-width: 110px;">
												{{Icône}}
											</span>
											<input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="battery::img" disabled/>
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
								
								<!-- COLOR BATTERY -->
								<div class="alert alert-info col-xs-12" style="text-align: center;margin-bottom: 15px;">
									{{Couleur personnalisée de l'icône batterie en fonction de la charge restante.}}<br>
									<i class="fas fa-exclamation-triangle"></i> {{Attention, cette fonctionnalité sera désactivée si vous utilisez une icône perso. ou une icône intègrée.}}
								</div>	
								<div class="form-group">
									<div class="col-lg-4">
										<div class="input-group">
											<span class="input-group-addon roundedLeft" style="min-width: 110px;">% = 0</span>
											<input type="color" class="eqLogicAttr form-control" value="#ff0000" data-l1key="configuration" data-l2key="battery::color::state::0" />
											<span class="input-group-btn">
												<a class="btn btn-default restoreDefaut roundedRight" data-type="battery::color::state::0" data-defaut="#ff0000" title="{{Couleur par défaut}}"><i class="fas fa-eraser"></i></a>
											</span>
										</div>
										<div class="input-group">
											<span class="input-group-addon roundedLeft" style="min-width: 110px;">0 < % < 50</span>
											<input type="color" class="eqLogicAttr form-control" value="#ff4005" data-l1key="configuration" data-l2key="battery::color::state::25" />
											<span class="input-group-btn">
												<a class="btn btn-default restoreDefaut roundedRight" data-type="battery::color::state::25" data-defaut="#ff4005" title="{{Couleur par défaut}}"><i class="fas fa-eraser"></i></a>
											</span>
										</div>
										<div class="input-group">
											<span class="input-group-addon roundedLeft" style="min-width: 110px;">50 <= % < 75</span>
											<input type="color" class="eqLogicAttr form-control" value="#ffa500" data-l1key="configuration" data-l2key="battery::color::state::50" />
											<span class="input-group-btn">
												<a class="btn btn-default restoreDefaut roundedRight" data-type="battery::color::state::50" data-defaut="#ffa500" title="{{Couleur par défaut}}"><i class="fas fa-eraser"></i></a>
											</span>
										</div>
										<div class="input-group">
											<span class="input-group-addon roundedLeft" style="min-width: 110px;">75 <= % < 100</span>
											<input type="color" class="eqLogicAttr form-control" value="#9ACD32" data-l1key="configuration" data-l2key="battery::color::state::75" />
											<span class="input-group-btn">
												<a class="btn btn-default restoreDefaut roundedRight" data-type="battery::color::state::75" data-defaut="#9ACD32" title="{{Couleur par défaut}}"><i class="fas fa-eraser"></i></a>
											</span>
										</div>
										<div class="input-group">
											<span class="input-group-addon roundedLeft" style="min-width: 110px;">% = 100</span>
											<input type="color" class="eqLogicAttr form-control" value="#008000" data-l1key="configuration" data-l2key="battery::color::state::100" />
											<span class="input-group-btn">
												<a class="btn btn-default restoreDefaut roundedRight" data-type="battery::color::state::100" data-defaut="#008000" title="{{Couleur par défaut}}"><i class="fas fa-eraser"></i></a>
											</span>
										</div>
									</div>
								</div>
								<br>
							</fieldset>
						</form>
					</div>
				</div>
				
				<!-- //////  ENERGY  ////// -->
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title"><i class="fas fas fa-bolt"></i> {{Energie quotidienne}}</h3>
					</div>
					<div class="panel-body">
						<form class="form-horizontal">
							<fieldset>
								<!-- //////  Energy power  ////// -->
								<div class="form-group">
									<div class="col-lg-1">
										<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="battery::daily::charge::activate">{{Activer}}</label>
									</div>
									<div class="col-lg-5">
										<div class="input-group">
											<span class="input-group-addon roundedLeft" style="min-width: 150px;">
												{{Charge}} <sup><i class="fas fa-question-circle" title="{{Selectionner une commande info/numerique qui contient l'information d'énergie de charge.}}"></i></sup>
											</span>
											<input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="battery::daily::charge::cmd"/>
											<span class="input-group-btn">
												<a class="btn btn-default listCmdInfo roundedRight" data-type="battery::daily::charge::cmd" data-subtype="numeric"><i class="fas fa-list-alt"></i></a>
											</span>
										</div>
									</div>
									<div class="col-lg-2">
										<div class="input-group">
											<span class="input-group-addon roundedLeft" style="min-width: 110px;">{{Texte à afficher}}</span>
											<input class="eqLogicAttr form-control roundedRight" data-l1key="configuration" data-l2key="battery::daily::charge::txt" placeholder="{{CHARGE JOUR}}" />
										</div>
									</div>
									<div class="col-lg-2">
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
										<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="battery::daily::discharge::activate">{{Activer}}</label>
									</div>
									<div class="col-lg-5">
										<div class="input-group">
											<span class="input-group-addon roundedLeft" style="min-width: 150px;">
												{{Décharge}} <sup><i class="fas fa-question-circle" title="{{Selectionner une commande info/numerique qui contient l'information d'énergie de décharge.}}"></i></sup>
											</span>
											<input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="battery::daily::discharge::cmd"/>
											<span class="input-group-btn">
												<a class="btn btn-default listCmdInfo roundedRight" data-type="battery::daily::discharge::cmd" data-subtype="numeric"><i class="fas fa-list-alt"></i></a>
											</span>
										</div>
									</div>
									<div class="col-lg-2">
										<div class="input-group">
											<span class="input-group-addon roundedLeft" style="min-width: 110px;">{{Texte à afficher}}</span>
											<input class="eqLogicAttr form-control roundedRight" data-l1key="configuration" data-l2key="battery::daily::discharge::txt" placeholder="{{DECHARGE JOUR}}" />
										</div>
									</div>
									<div class="col-lg-2">
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
                <!-- //////  MPPT  ////// -->
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title"><i class="fas fas fa-bolt"></i> {{Panneau solaire}}</h3>
					</div>
					<div class="panel-body">
						<form class="form-horizontal">
							<fieldset>
								<!-- mptt power -->
								<div class="form-group">
									<div class="col-lg-1">
										<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="battery::mppt::power::activate">{{Activer}}</label>
									</div>
									<div class="col-lg-4">
										<div class="input-group">
											<span class="input-group-addon roundedLeft" style="min-width: 110px;">
												{{Puissance}} <sup><i class="fas fa-question-circle" title="{{Selectionner une commande info/numerique qui contient l'information de puissance.}}"></i></sup>
											</span>
											<input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="battery::mppt::power::cmd" />
											<span class="input-group-btn">
												<a class="btn btn-default listCmdInfo roundedRight" data-type="battery::mppt::power::cmd" data-subtype="numeric"><i class="fas fa-list-alt"></i></a>
											</span>
										</div>
									</div>
									<div class="col-lg-2">
										<div class="input-group">
											<span class="input-group-addon roundedLeft" style="min-width: 110px;">
												{{Nom}} <sup><i class="fas fa-question-circle" title="{{Nom à afficher.}}"></i></sup>
											</span>
											<input class="eqLogicAttr form-control roundedRight" data-l1key="configuration" data-l2key="battery::mppt::name">
										</div>
									</div>
									<div class="col-lg-2">
										<div class="input-group">
											<span class="input-group-addon roundedLeft" style="min-width: 150px;">{{Couleur}}</span>
											<input type="color" class="eqLogicAttr form-control" value="#ffa500" data-l1key="configuration" data-l2key="battery::mppt::color" />
											<span class="input-group-btn">
												<a class="btn btn-default restoreDefaut roundedRight" data-type="battery::mppt::color" data-defaut="#ffa500" title="{{Couleur par défaut}}"><i class="fas fa-eraser"></i></a>
											</span>
										</div>
									</div>
								</div>
								
								<!--  mppt energy  -->
								<div class="form-group">
									<div class="col-lg-1">
										<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="battery::mppt::energy::activate">{{Activer}}</label>
									</div>
									<div class="col-lg-4">
										<div class="input-group">
											<span class="input-group-addon roundedLeft" style="min-width: 110px;">
												{{Energie}} <sup><i class="fas fa-question-circle" title="{{Selectionner une commande info/numerique qui contient l'information d'énergie.}}"></i></sup>
											</span>
											<input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="battery::mppt::energy::cmd" />
											<span class="input-group-btn">
												<a class="btn btn-default listCmdInfo roundedRight" data-type="battery::mppt::energy::cmd" data-subtype="numeric"><i class="fas fa-list-alt"></i></a>
											</span>
										</div>
									</div>
								</div>
							</fieldset>
						</form>
					</div>
				</div>
			</div>
			<!-- 
			////////////////////////////
			//////////  LOAD  //////////
			//////////////////////////// 
			-->
			<div class="tab-pane" id="configureLoad">
				<br>
				<!-- LOAD CONFIGURATION -->
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title"><i class="fas fas fa-bolt"></i> {{Configuration}}</h3>
					</div>
					<div class="panel-body">
						<form class="form-horizontal">
							<fieldset>
								<div class="alert alert-info col-xs-12" style="text-align: center;margin-bottom: 15px;">
									{{Si vous disposez déja d'une commande contenant la puissance totale des recepteurs, vous pouvez renseigner celle-ci ci-dessous.}}
									<br>{{Le widget prendra en compte cette valeur a défaut de calculer la somme des puissances de chaques recepteurs.}}
								</div>
								<div class="form-group">
									<div class="col-lg-1">
										<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="load::power::activate">{{Activer}}</label>
									</div>
									<div class="col-lg-4">
										<div class="input-group">
											<span class="input-group-addon roundedLeft" style="min-width: 110px;">{{Puissance}} <sup><i class="fas fa-question-circle" title="{{Sélectionnez une commande info/numérique contenant les informations de puissance totale des recepteurs.}}<br>{{Laisser vide si vous voulez que le widget calcul la somme des recepteurs.}}"></i></sup></span>
											<input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="load::power::cmd">
											<span class="input-group-btn">
												<a class="btn btn-default listCmdInfo roundedRight" data-type="load::power::cmd" data-subtype="numeric"><i class="fas fa-list-alt"></i></a>
											</span>
										</div>
									</div>
								</div>
								<hr class="hrPrimary">
								<div class="form-group">
									<div class="col-lg-2 col-xs-offset-1">
										<div class="input-group">
											<span class="input-group-addon roundedLeft" style="min-width: 110px;">{{Couleur}}</span>
											<input type="color" class="eqLogicAttr form-control" value="#5fb6ad" data-l1key="configuration" data-l2key="load::color">
											<span class="input-group-btn">
												<a class="btn btn-default restoreDefaut roundedRight" data-type="load::color" data-defaut="#5fb6ad" title="{{Couleur par défaut}}"><i class="fas fa-eraser"></i></a>
											</span>
										</div>
									</div>
									<div class="col-lg-2">
										<div class="input-group">
											<span class="input-group-addon roundedLeft" style="min-width: 110px;">{{Max.}} <sup><i class="fas fa-question-circle" title="{{Puissance maximale de consommation.}}"></i></sup></span>
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
										<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="load::daily::activate">{{Activer}}</label>
									</div>
									<div class="col-lg-4">
										<div class="input-group">
											<span class="input-group-addon roundedLeft" style="min-width: 110px;">{{Commande}} <sup><i class="fas fa-question-circle" title="{{Selectionner une commande info/numerique qui contient l'information d'énergie.}}"></i></sup></span>
											<input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="load::daily::cmd"/>
											<span class="input-group-btn">
												<a class="btn btn-default listCmdInfo roundedRight" data-type="load::daily::cmd" data-subtype="numeric"><i class="fas fa-list-alt"></i></a>
											</span>
										</div>
									</div>
									<div class="col-lg-2">
										<div class="input-group">
											<span class="input-group-addon roundedLeft" style="min-width: 110px;">{{Texte à afficher}}</span>
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
						<h3 class="panel-title"><i class="fas fa-solar-panel"></i> {{Recepteurs}}</h3>
					</div>
					<div class="panel-body">
						<form class="form-horizontal">
							<fieldset>
								<legend>
									<a class="btn btn-sm btn-success addLoad" data-type="load" data-subType="numeric"><i class="fas fa-plus-circle"></i> {{Ajouter des recepteurs}}</a>
								</legend>
								<div id="div_load" class="col-xs-12"></div>
							</fieldset>
						</form>
					</div>
				</div>
			</div>
            
			<!-- 
			////////////////////////////
			////////  INVERTER  ////////
			//////////////////////////// 
			-->
			<div class="tab-pane" id="configureInverter">
				<br>
				<!-- INVERTER CONFIGURATION -->
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title"><i class="fas fas fa-bolt"></i> {{Configuration}}</h3>
					</div>
					<div class="panel-body">
						<form class="form-horizontal">
							<fieldset>
								<div class="alert alert-info col-xs-12" style="text-align: center;margin-bottom: 15px;">
									{{Si vous disposez déja d'une commande contenant la puissance totale des recepteurs, vous pouvez renseigner celle-ci ci-dessous.}}
									<br>{{Le widget prendra en compte cette valeur a défaut de calculer la somme des puissances de chaques recepteurs.}}
								</div>
								<div class="form-group">
									<div class="col-lg-4">
										<div class="input-group">
											<span class="input-group-addon roundedLeft" style="min-width: 110px;">{{Tension}} <sup><i class="fas fa-question-circle" title="{{Sélectionnez une commande info/numérique contenant l'informations de tension.}}"></i></sup></span>
											<input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="inverter::voltage::cmd">
											<span class="input-group-btn">
												<a class="btn btn-default listCmdInfo roundedRight" data-type="inverter::voltage::cmd" data-subtype="numeric"><i class="fas fa-list-alt"></i></a>
											</span>
										</div>
									</div>
									<div class="col-lg-4">
										<div class="input-group">
											<span class="input-group-addon roundedLeft" style="min-width: 110px;">{{Fréquence}} <sup><i class="fas fa-question-circle" title="{{Sélectionnez une commande info/numérique contenant l'informations de fréquence.}}"></i></sup></span>
											<input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="inverter::frequency::cmd">
											<span class="input-group-btn">
												<a class="btn btn-default listCmdInfo roundedRight" data-type="inverter::frequency::cmd" data-subtype="numeric"><i class="fas fa-list-alt"></i></a>
											</span>
										</div>
									</div>
									<div class="col-lg-4">
										<div class="input-group">
											<span class="input-group-addon roundedLeft" style="min-width: 110px;">{{Intensité}} <sup><i class="fas fa-question-circle" title="{{Sélectionnez une commande info/numérique contenant l'informations d'intensité.}}"></i></sup></span>
											<input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="inverter::current::cmd">
											<span class="input-group-btn">
												<a class="btn btn-default listCmdInfo roundedRight" data-type="inverter::current::cmd" data-subtype="numeric"><i class="fas fa-list-alt"></i></a>
											</span>
										</div>
									</div>
								</div>
								<div class="form-group">
									<div class="col-lg-4">
										<div class="input-group">
											<span class="input-group-addon roundedLeft" style="min-width: 110px;">{{LCD}} <sup><i class="fas fa-question-circle" title="{{Sélectionnez une commande info à afficher dans le lcd.}}"></i></sup></span>
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
			</div>
        
		</div>
	</div>
</div>
<?php include_file('desktop', 'powerFlow', 'js', 'powerFlow'); ?>
<?php include_file('core', 'plugin.template', 'js'); ?>