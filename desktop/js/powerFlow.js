/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

function addCmdToTable(_cmd) {
  if (document.getElementById('table_cmd') == null) return
  if (!isset(_cmd)) {
    var _cmd = { configuration: {} }
  }
  if (!isset(_cmd.configuration)) {
    _cmd.configuration = {}
  }
  var tr = '<tr>'
  tr += '<td class="hidden-xs">'
  tr += '<span class="cmdAttr" data-l1key="id"></span>'
  tr += "</td>"
  tr += "<td>"
  tr += '<div class="input-group">'
  tr += '<input class="cmdAttr form-control input-sm roundedLeft" data-l1key="name" placeholder="{{Nom de la commande}}">'
  tr += '<span class="input-group-btn"><a class="cmdAction btn btn-sm btn-default" data-l1key="chooseIcon" title="{{Choisir une icône}}"><i class="fas fa-icons"></i></a></span>'
  tr += '<span class="cmdAttr input-group-addon roundedRight" data-l1key="display" data-l2key="icon" style="font-size:19px;padding:0 5px 0 0!important;"></span>'
  tr += "</div>"
  tr += "<td>"
  tr += '<span class="type" type="' + init(_cmd.type) + '">' + jeedom.cmd.availableType() + "</span>"
  tr += '<span class="subType" subType="' + init(_cmd.subType) + '"></span>'
  tr += "</td>"
  tr += "<td>"
  tr += '<label class="checkbox-inline"><input type="checkbox" class="cmdAttr" data-l1key="isVisible" checked/>{{Afficher}}</label> '
  tr += '<label class="checkbox-inline"><input type="checkbox" class="cmdAttr" data-l1key="isHistorized" checked/>{{Historiser}}</label> '
  tr += '<div style="margin-top:7px;">'
  tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="minValue" placeholder="{{Min}}" title="{{Min}}" style="width:30%;max-width:80px;display:inline-block;margin-right:2px;">'
  tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="maxValue" placeholder="{{Max}}" title="{{Max}}" style="width:30%;max-width:80px;display:inline-block;margin-right:2px;">'
  tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="unite" placeholder="Unité" title="{{Unité}}" style="width:30%;max-width:80px;display:inline-block;margin-right:2px;">'
  tr += "</div>"
  tr += "<td>"
  tr += '<span class="cmdAttr" data-l1key="htmlstate"></span>'
  tr += "</td>"
  tr += "<td>"
  if (is_numeric(_cmd.id)) {
    tr += '<a class="btn btn-default btn-xs cmdAction" data-action="configure" title="{{Configuration avancée}}"><i class="fas fa-cogs"></i></a> '
    tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fas fa-rss"></i> {{Tester}}</a>'
  }
  tr += "</td>"
  tr += "<td>"
  if (init(_cmd.logicalId) !== "notif" && init(_cmd.logicalId) !== "notifCritical") {
    tr += '<i class="fas fa-minus-circle pull-right cmdAction cursor" data-action="remove" title="{{Supprimer la commande}}"></i>'
  }
  tr += "</td>"
  tr += "</tr>"
  let newRow = document.createElement('tr')
  newRow.innerHTML = tr
  newRow.addClass('cmd')
  newRow.setAttribute('data-cmd_id', init(_cmd.id))
  document.getElementById('table_cmd').querySelector('tbody').appendChild(newRow)
  jeedom.eqLogic.buildSelectCmd({
    id: document.querySelector('.eqLogicAttr[data-l1key="id"]').jeeValue(),
    filter: { type: 'info' },
    error: function(error) {
      jeedomUtils.showAlert({ message: error.message, level: 'danger' })
    },
    success: function(result) {
      newRow.setJeeValues(_cmd, '.cmdAttr')
      jeedom.cmd.changeType(newRow, init(_cmd.subType))
    }
  })
}
////  GRID  \\\\
/*
document.querySelectorAll('#configureGrid .listCmdInfo').forEach(listCmdInfo => {
  listCmdInfo.addEventListener('click', function(event) {
    var type = this.getAttribute('data-type')
    var subtype = this.getAttribute('data-subtype')
    if (el = document.querySelector('#configureGrid .eqLogicAttr[data-l2key="' + type + '"]')) {
      jeedom.cmd.getSelectModal({
        cmd: {
          type: 'info',
          subType: subtype
        }
      }, function(result) {
        el.jeeValue(result.human)
        jeeFrontEnd.modifyWithoutSave = true
      })
    }
  })
})
*/
////  BATTERY  \\\\
/*
document.querySelectorAll('#configureBattery .listCmdInfo').forEach(listCmdInfo => {
  listCmdInfo.addEventListener('click', function(event) {
    var type = this.getAttribute('data-type')
    var subtype = this.getAttribute('data-subtype')
    if (el = document.querySelector('#configureBattery .eqLogicAttr[data-l2key="' + type + '"]')) {
      jeedom.cmd.getSelectModal({
        cmd: {
          type: 'info',
          subType: subtype
        }
      }, function(result) {
        el.jeeValue(result.human)
        jeeFrontEnd.modifyWithoutSave = true
      })
    }
  })
})
*/
////  SOLAR  \\\\
document.querySelector('.addPv').addEventListener('click', function(event) {
  addPv({})
})
////  LOAD  \\\\
document.querySelector('.addLoad').addEventListener('click', function(event) {
  addLoad({})
})

document.querySelector('#div_pv').addEventListener('click', function(event) {
  var _target = null
  var _type = null
  if (_target = event.target.closest('.bt_removeInfo')) {
    _target.closest('.pv').remove()
    return;
  } else if (_target = event.target.closest('.listCmdInfo[data-type][data-subtype]')) {
    console.log('listCmdInfo pv')
    event.stopPropagation()
    event.preventDefault()
    var type = _target.getAttribute('data-type')
    var el = _target.closest('.pv').querySelector('.pvAttr[data-l1key="' + type + '"]')
    jeedom.cmd.getSelectModal({
      cmd: {
        type: 'info',
        subType: _target.getAttribute('data-subtype')
      }
    }, function(result) {
      el.jeeValue(result.human)
      jeeFrontEnd.modifyWithoutSave = true
    })
    return;
  }
})
document.querySelector('#div_load').addEventListener('click', function(event) {
  var _target = null
  var _type = null
  if (_target = event.target.closest('.bt_removeInfo')) {
    _target.closest('.load').remove()
    return;
  } else if (_target = event.target.closest('.listCmdInfo[data-type][data-subtype]')) {
    console.log('listCmdInfo load')
    event.stopPropagation()
    event.preventDefault()
    var type = _target.getAttribute('data-type')
    var el = _target.closest('.load').querySelector('.loadAttr[data-l1key="' + type + '"]')
    jeedom.cmd.getSelectModal({
      cmd: {
        type: 'info',
        subType: _target.getAttribute('data-subtype')
      }
    }, function(result) {
      el.jeeValue(result.human)
      jeeFrontEnd.modifyWithoutSave = true
    })
    return;
  } else if (_target = event.target.closest('.bt_library')) {
    console.log('bt_library load')
    event.stopPropagation()
    event.preventDefault()
    var type = _target.getAttribute('data-type')
    var el = _target.closest('.load').querySelector('.loadAttr[data-l1key="' + type + '"]')
    if (el) {
      let icon = el.value
      let params = {}
      params.icon = false
      params.img = true
      if (icon.value != '') {
        params.icon = icon.value
      }
      powerFlowChooseIcon(function(_icon) {
        el.value = _icon
      }, params)
    }
  } else if (_target = event.target.closest('.restoreDefaut[data-type][data-defaut]')) {
    console.log('restoreDefaut load')
    event.stopPropagation()
    event.preventDefault()
    var type = _target.getAttribute('data-type')
    var el = _target.closest('.load').querySelector('input[data-l1key="' + type + '"]')
    if (el) {
      el.value = _target.getAttribute('data-defaut')
    }
  }
})
document.querySelector('.tab-content').addEventListener('click', function(event) {
  console.log('click')
  var _target = null
  if (_target = event.target.closest('.restoreDefaut[data-type][data-defaut]')) {
    console.log('restoreDefaut 2')
    var el = document.querySelector('input[data-l2key="' + _target.getAttribute('data-type') + '"]')
    if (!el) el = document.querySelector('input[data-l1key="' + _target.getAttribute('data-type') + '"]')
    if (el) {
      el.value = _target.getAttribute('data-defaut')
    }
  }
  else if (_target = event.target.closest('.listCmdInfo[data-type][data-subtype]')) {
    console.log('listCmdInfo 2')
    var type = _target.getAttribute('data-type')
    var el = document.querySelector('input[data-l2key="' + type + '"]')
    if (el) {
      let params = {}
      params.type = 'info'
      if (_target.getAttribute('data-subtype') != '') params.subType = _target.getAttribute('data-subtype')
      jeedom.cmd.getSelectModal({
        cmd: params
        //cmd: {
          //type: 'info',
          //subType: _target.getAttribute('data-subtype')
        //}
      }, function(result) {
        el.jeeValue(result.human)
        jeeFrontEnd.modifyWithoutSave = true
      })
      return;
    } 
  } else if (_target = event.target.closest('.bt_library')) {
    console.log('bt_library 2')
    var el = document.querySelector('input[data-l2key="' + _target.getAttribute('data-type') + '"]')
    if (el) {
      console.log(el)
      let icon = el.value
      console.log(icon)
      let params = {}
      params.icon = false
      params.img = true
      if (icon.value != '') {
        params.icon = icon.value
      }
      powerFlowChooseIcon(function(_icon) {
        el.value = _icon
      }, params)
    }
  }
  return;
})
powerFlowChooseIcon = function(_callback, _params) {
  var url = 'index.php?v=d&plugin=powerFlow&modal=icon.selector'
  if (_params && _params.img && _params.img === true) {
    url += '&showimg=1'
  }
  if (_params && _params.icon) {
    var icon = _params.icon
    url += '&selectIcon=' + icon
  }
  if (_params && _params.path) {
    url += '&path=' + encodeURIComponent(_params.path)
  }
  jeeDialog.dialog({
    id: 'mod_selectIcon',
    title: '{{Choisir une illustration}}',
    width: (window.innerWidth - 50) < 1500 ? window.innerWidth - 50 : window.innerHeight - 150,
    height: window.innerHeight - 150,
    buttons: {
      confirm: {
        label: '{{Appliquer}}',
        className: 'success',
        callback: {
          click: function(event) {
            if (document.getElementById('mod_selectIcon').querySelector('.iconSelected .iconSel') === null) {
              jeeDialog.get('#mod_selectIcon').close()
              return
            }
            
            var icon = document.getElementById('mod_selectIcon').querySelector('.iconSelected .iconSel').innerHTML
            if (icon == undefined) {
              icon = ''
            }
            if(icon.indexOf('<svg') === 0) {
              icon = document.getElementById('mod_selectIcon').querySelector('.iconSelected .iconSel svg').getAttribute('data-icon')
            }
            if(icon.indexOf('<img') === 0){
              icon = document.getElementById('mod_selectIcon').querySelector('.iconSelected .iconSel img').getAttribute('data-realfilepath')
              
            }
            icon = icon.replace(/"/g, "'")
            _callback(icon)
            jeeDialog.get('#mod_selectIcon').close()
          }
        }
      },
      cancel: {
        label: '{{Annuler}}',
        className: 'warning',
        callback: {
          click: function(event) {
            jeeDialog.get('#mod_selectIcon').close()
          }
        }
      }
    },
    onClose: function() {
      jeeDialog.get('#mod_selectIcon').destroy() //No twice footer select/search
    },
    contentUrl: url
  })
}


function addPv(_action) {
  var div = '<div class="pv">'
    div += '<div class="form-group">'
      // Activate
      div += '<div class="col-lg-1">'
        div += '<a class="bt_removeInfo pull-left" style="margin-right: 15px;" data-type="pv"><i class="fas fa-minus-circle"></i></a>'
        div += '<label class="checkbox-inline"><input type="checkbox" class="pvAttr cmdInfo" data-l1key="power::activate">{{Activer}}</label>'
      div += '</div>'
      // Power
      div += '<div class="col-lg-3">'
        div += '<div class="input-group">'
          div += '<span class="input-group-addon roundedLeft" style="min-width: 110px;">{{Puissance}} <i class="fas fa-exclamation-triangle warning" title="'
          div += "{{Selectionner une commande info/numerique qui contient l'information de puissance.}}"
          div += '<br>{{Commande obligatoire.}}"></i></span>'
          div += '<input class="pvAttr form-control" data-l1key="power::cmd" data-type="pv" />' // cmdAction
          div += '<span class="input-group-btn">'
            div += '<a class="btn btn-default listCmdInfo roundedRight" data-type="power::cmd" data-subtype="numeric"><i class="fas fa-list-alt"></i></a>' //data-l1key="power::cmd"
          div += '</span>'
        div += '</div>'
      div += '</div>'
      div += '<div class="col-lg-3">'
        // Voltage
        div += '<div class="input-group">'
          div += '<span class="input-group-addon roundedLeft" style="min-width: 110px;">{{Tension}} <sup><i class="fas fa-question-circle" title="'
          div += "{{Selectionner une commande info/numerique qui contient l'information de tension.}}"
          div += '"></i></sup></span>'
          div += '<input class="pvAttr form-control" data-l1key="voltage::cmd" data-type="pv" />' // cmdAction
          div += '<span class="input-group-btn">'
            div += '<a class="btn btn-default listCmdInfo roundedRight" data-type="voltage::cmd" data-subtype="numeric"><i class="fas fa-list-alt"></i></a>'
          div += '</span>'
        div += '</div>'
        // Current
        div += '<div class="input-group">'
          div += '<span class="input-group-addon roundedLeft" style="min-width: 110px;">{{Intensité}} <sup><i class="fas fa-question-circle" title="'
          div += "{{Selectionner une commande info/numerique qui contient l'information d'intensité.}}"
          div += '"></i></sup></span>'
          div += '<input class="pvAttr form-control" data-l1key="current::cmd" data-type="pv" />' // cmdAction
          div += '<span class="input-group-btn">'
            div += '<a class="btn btn-default listCmdInfo roundedRight" data-type="current::cmd" data-subtype="numeric"><i class="fas fa-list-alt"></i></a>'
          div += '</span>'
        div += '</div>'
      div += '</div>'
      div += '<div class="col-lg-3">'
        // Energy
        div += '<div class="input-group">'
          div += '<span class="input-group-addon roundedLeft" style="min-width: 110px;">{{Energie}} <sup><i class="fas fa-question-circle" title="'
          div += "{{Selectionner une commande info/numerique qui contient l'information d'énergie.}}"
          div += '"></i></sup></span>'
          div += '<input class="pvAttr form-control" data-l1key="energy::cmd" data-type="pv" />' // cmdAction
          div += '<span class="input-group-btn">'
            div += '<a class="btn btn-default listCmdInfo roundedRight" data-type="energy::cmd" data-subtype="numeric"><i class="fas fa-list-alt"></i></a>'
          div += '</span>'
        div += '</div>'
        // name & Powermax
        div += '<div class="input-group">'
          div += '<span class="input-group-addon roundedLeft" style="min-width: 110px;">{{Nom}} <sup><i class="fas fa-question-circle" title="{{Nom a afficher pour identifier le panneau.}}"></i></sup></span>'
          div += '<div><input class="pvAttr form-control" data-l1key="name"></div>'
          div += '<span class="input-group-addon" style="min-width: 110px;">Max. <sup><i class="fas fa-question-circle" title="{{Puissance maximale que peut produire le panneau.}}"></i></sup></span>'
          div += '<div class="roundedRight"><input type="number" class="pvAttr form-control roundedRight" data-l1key="maxPower"></div>'
        div += '</div>'
      div += '</div>'
  
    div += '</div>'
  div += '</div>'
  document.getElementById('div_pv').insertAdjacentHTML('beforeend', div)
  var currentPv = document.querySelectorAll('.pv').last()
  currentPv.setJeeValues(_action, '.pvAttr')
  jeedomUtils.initTooltips(currentPv)
}

function addLoad(_action) {
  var div = '<div class="load">'
    div += '<div class="form-group">'
      // Activate
      div += '<div class="col-lg-1">'
        div += '<a class="bt_removeInfo pull-left" style="margin-right: 15px;" data-type="load"><i class="fas fa-minus-circle"></i></a>'
        div += '<label class="checkbox-inline"><input type="checkbox" class="loadAttr cmdInfo" data-l1key="power::activate">{{Activer}}</label>'
      div += '</div>'
      // Power
      div += '<div class="col-lg-3">'
        div += '<div class="input-group">'
          div += '<span class="input-group-addon roundedLeft" style="min-width: 110px;">{{Puissance}} <i class="fas fa-exclamation-triangle warning" title="'
          div += "{{Selectionner une commande info/numerique qui contient l'information de puissance.}}"
          div += '<br>{{Commande obligatoire.}}"></i></span>'
          div += '<input class="loadAttr form-control" data-l1key="power::cmd" data-type="load" />' // cmdAction
          div += '<span class="input-group-btn">'
            div += '<a class="btn btn-default listCmdInfo roundedRight" data-type="power::cmd" data-subtype="numeric"><i class="fas fa-list-alt"></i></a>' //data-l1key="power::cmd"
          div += '</span>'
        div += '</div>'
        // Perso
        div += '<div class="input-group">'
          div += '<span class="input-group-addon roundedLeft" style="min-width: 110px;">{{Energie}} <sup><i class="fas fa-question-circle" title="'
          div += "{{Selectionner une commande info/numerique qui contient l'information d'énergie.}}"
          div += '"></i></sup></span>'
          div += '<input class="loadAttr form-control" data-l1key="energy::cmd" data-type="load" />' // cmdAction
          div += '<span class="input-group-btn">'
            div += '<a class="btn btn-default listCmdInfo roundedRight" data-type="energy::cmd" data-subtype="numeric"><i class="fas fa-list-alt"></i></a>'
          div += '</span>'
        div += '</div>'
      div += '</div>'
      div += '<div class="col-lg-3">'
        // Energy
        div += '<div class="input-group">'
          div += '<span class="input-group-addon roundedLeft" style="min-width: 110px;">{{Perso}} <sup><i class="fas fa-question-circle" title="'
          div += "{{Selectionner une commande info/numerique qui contient l'information perso.}}"
          div += '"></i></sup></span>'
          div += '<input class="loadAttr form-control" data-l1key="perso::cmd" data-type="load" />' // cmdAction
          div += '<span class="input-group-btn">'
            div += '<a class="btn btn-default listCmdInfo roundedRight" data-type="perso::cmd" data-subtype="numeric"><i class="fas fa-list-alt"></i></a>'
          div += '</span>'
        div += '</div>'
        // name & Powermax
        div += '<div class="input-group">'
          div += '<span class="input-group-addon roundedLeft" style="min-width: 110px;">{{Nom}} <sup><i class="fas fa-question-circle" title="{{Nom a afficher pour identifier le recepteur.}}"></i></sup></span>'
          div += '<div><input class="loadAttr form-control" data-l1key="name"></div>'
          div += '<span class="input-group-addon" style="min-width: 110px;">Max. <sup><i class="fas fa-question-circle" title="{{Puissance maximale que peut consommer le recepteur.}}"></i></sup></span>'
          div += '<div class="roundedRight"><input type="number" class="loadAttr form-control roundedRight" data-l1key="maxPower"></div>'
        div += '</div>'
      div += '</div>'

      div += '<div class="col-lg-3">'
  div += '<div class="input-group">'
    div += '<span class="input-group-addon roundedLeft input-group-addon" style="min-width: 110px;">'
      div += '{{Icône}} 1'
    div += '</span>'
    div += '<input class="loadAttr form-control" data-l1key="img::1" disabled>'
    div += '<span class="input-group-btn">'
      div += '<a class="btn btn-default bt_library" data-type="img::1" title="{{Bibliothèque}}"><i class="fas fa-photo-video"></i></a>'
    div += '</span>'
    div += '<span class="input-group-btn">'
      div += '<a class="btn btn-default restoreDefaut roundedRight" data-type="img::1" data-defaut="" title="{{Icône par défaut}}">'
        div += '<i class="fas fa-eraser"></i>'
      div += '</a>'
    div += '</span>'
  div += '</div>'
  div += '<div class="input-group">'
    div += '<span class="input-group-addon roundedLeft input-group-addon" style="min-width: 110px;">'
      div += '{{Icône}} 2'
    div += '</span>'
    div += '<input class="loadAttr form-control" data-l1key="img::2" disabled>'
    div += '<span class="input-group-btn">'
      div += '<a class="btn btn-default bt_library" data-type="img::2" title="{{Bibliothèque}}"><i class="fas fa-photo-video"></i></a>'
    div += '</span>'
    div += '<span class="input-group-btn">'
      div += '<a class="btn btn-default restoreDefaut roundedRight" data-type="img::2" data-defaut="" title="{{Icône par défaut}}">'
        div += '<i class="fas fa-eraser"></i>'
      div += '</a>'
    div += '</span>'
      div += '</div>'

    div += '</div>'
  div += '</div>'
  document.getElementById('div_load').insertAdjacentHTML('beforeend', div)
  var currentLoad = document.querySelectorAll('.load').last()
  currentLoad.setJeeValues(_action, '.loadAttr')
  jeedomUtils.initTooltips(currentLoad)
}


function saveEqLogic(_eqLogic) {
  if (!isset(_eqLogic.configuration)) {
    _eqLogic.configuration = {}
  }
  _eqLogic.configuration.pv = $('#div_pv .pv').getValues('.pvAttr')
  _eqLogic.configuration.load = $('#div_load .load').getValues('.loadAttr')
  return _eqLogic;
}

new Sortable(document.getElementById('div_pv'), {
    delay: 50,
    delayOnTouchOnly: true,
    draggable: '.pv',
    filter: '.pvAttr, .btn, label, a',
    preventOnFilter: false,
    direction: 'vertical',
    chosenClass: 'dragSelected',
    onUpdate: function(evt) {
      jeeFrontEnd.modifyWithoutSave = true
    }
  })
  new Sortable(document.getElementById('div_load'), {
    delay: 50,
    delayOnTouchOnly: true,
    draggable: '.load',
    filter: '.loadAttr, .btn, label, a',
    preventOnFilter: false,
    direction: 'vertical',
    chosenClass: 'dragSelected',
    onUpdate: function(evt) {
      jeeFrontEnd.modifyWithoutSave = true
    }
  })
function prePrintEqLogic(_eqlogicId){
  document.getElementById('div_pageContainer').querySelectorAll('input.eqLogicAttr').forEach(_input => {
    if (_input.getAttribute('type') == 'checkbox' && _input.checked) _input.checked = false
  })
  console.log(_eqlogicId)
}
function printEqLogic(_eqLogic) {
  console.log(_eqLogic)
  document.getElementById('div_pv').empty()
  document.getElementById('div_load').empty()
  if (isset(_eqLogic.configuration)) {
    if (isset(_eqLogic.configuration.pv)) {
      for (var i in _eqLogic.configuration.pv) {
        addPv(_eqLogic.configuration.pv[i])
      }
    }
    if (isset(_eqLogic.configuration.load)) {
      for (var i in _eqLogic.configuration.load) {
        addLoad(_eqLogic.configuration.load[i])
      }
    }
    if (!isset(_eqLogic.configuration['solar::color'])) document.querySelector('input[data-l2key="solar::color"]').value = '#ffa500'
    if (!isset(_eqLogic.configuration['solar::color::0'])) document.querySelector('input[data-l2key="solar::color::0"]').value = '#ffa500'
    if (!isset(_eqLogic.configuration['grid::color'])) document.querySelector('input[data-l2key="grid::color"]').value = '#5490c2'
    if (!isset(_eqLogic.configuration['grid::color::sell'])) document.querySelector('input[data-l2key="grid::color::sell"]').value = '#5490c2'
    if (!isset(_eqLogic.configuration['grid::color::buy'])) document.querySelector('input[data-l2key="grid::color::buy"]').value = '#5490c2'
    if (!isset(_eqLogic.configuration['grid::color::nogrid'])) document.querySelector('input[data-l2key="grid::color::nogrid"]').value = '#db041c'
    if (!isset(_eqLogic.configuration['battery::color'])) document.querySelector('input[data-l2key="battery::color"]').value = '#ffc0cb'
    if (!isset(_eqLogic.configuration['battery::color::charge'])) document.querySelector('input[data-l2key="battery::color::charge"]').value = '#ffc0cb'
    if (!isset(_eqLogic.configuration['battery::color::discharge'])) document.querySelector('input[data-l2key="battery::color::discharge"]').value = '#ffc0cb'
    if (!isset(_eqLogic.configuration['battery::color::state::0'])) document.querySelector('input[data-l2key="battery::color::state::0"]').value = '#ff0000'
    if (!isset(_eqLogic.configuration['battery::color::state::25'])) document.querySelector('input[data-l2key="battery::color::state::25"]').value = '#FF4500'
    if (!isset(_eqLogic.configuration['battery::color::state::50'])) document.querySelector('input[data-l2key="battery::color::state::50"]').value = '#ffa500'
    if (!isset(_eqLogic.configuration['battery::color::state::75'])) document.querySelector('input[data-l2key="battery::color::state::75"]').value = '#9ACD32'
    if (!isset(_eqLogic.configuration['battery::color::state::100'])) document.querySelector('input[data-l2key="battery::color::state::100"]').value = '#008000'
    if (!isset(_eqLogic.configuration['battery::mppt::color'])) document.querySelector('input[data-l2key="battery::mppt::color"]').value = '#ffa500'
    if (!isset(_eqLogic.configuration['load::color'])) document.querySelector('input[data-l2key="load::color"]').value = '#5fb6ad'
    

  }
  /*document.querySelector('.tab-content').addEventListener('change', function(event) {
    console.log('change')
    var _target = null
    if (event.target.getAttribute('data-l2key') && event.target.getAttribute('data-l2key').indexOf("activate") !== -1) return;
    if (_target = event.target.closest('.form-group')) {
      if (_target.querySelector('input[type="checkbox"]')) {
        if (event.target.getAttribute('data-l2key') && event.target.getAttribute('data-l2key').indexOf("cmd") !== -1) {
          if (event.target.value == '') _target.querySelector('input[type="checkbox"]').checked = false
          else _target.querySelector('input[type="checkbox"]').checked = true
        } else if (event.target.getAttribute('data-l1key') && event.target.getAttribute('data-l1key').indexOf("power::cmd") !== -1) {
          if (event.target.value == '') _target.querySelector('input[type="checkbox"]').checked = false
          else _target.querySelector('input[type="checkbox"]').checked = true
        //} else if (event.target.getAttribute('data-l2key') && event.target.getAttribute('data-l2key').indexOf("color") !== -1) {
          //if (event.target.value == '') _target.querySelector('input[type="checkbox"]').checked = false
          //else _target.querySelector('input[type="checkbox"]').checked = true
        }
        return;
      }
    }
    return;
  })
  */
}
