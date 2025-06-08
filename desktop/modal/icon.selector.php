<?php
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

if (!isConnect()) {
  throw new Exception('401 - {{Accès non autorisé}}');
}

/*
  Object, widget, etc icon select -> display only icon tree
  Widget replace select -> display icon tree and user image /data/img tree
  Object background select -> display gallery image core/img/object_background
*/

$icon_Struct = [];
$userImg_Struct = [];
$gal_Struct = [];


$xml_url = __DIR__ . '/../../desktop/images/icon.svg';
//$xml_url = __DIR__ . '/../../core/template/dashboard/icon.svg';
log::add('powerFlow', 'debug', $xml_url);
$contenu = simplexml_load_file($xml_url);
<<<<<<< HEAD
foreach ($contenu as $icon){
=======
foreach ($contenu as $icon) {
>>>>>>> 09bf1b5c696b0609542e9683292196349f11ef83
  if (isset($icon['id'])) {
    $nameIcon = str_replace('icon-', '', $icon['id']);
    $nameIconTranslate = __($nameIcon, __FILE__);
    if (isset($icon['data-categorie'])) {
      $icon_Struct[ucfirst(__($icon['data-categorie'], __FILE__))][] = array($nameIcon => $nameIconTranslate);
    } else $icon_Struct[__('Autre', __FILE__)][] = array($nameIcon => __($nameIcon, __FILE__));
  }
}
ksort($icon_Struct);


if (init('showImg') == 1) {
  //Build $userImg_Struct for js tree:
  $userImg_Struct['rootPath'] = __DIR__ . '/../../../../data/img/';
}

sendVarToJS([
  'jeephp2js.md_iconSelector_selectIcon' => init('selectIcon', 0),
  //'jeephp2js.md_iconSelector_colorIcon' => init('colorIcon', 0),
  'jeephp2js.showImg' => init('showImg', 0),
  'jeephp2js.showIcon' => init('showIcon', 0),
  'jeephp2js.icon_Struct' => $icon_Struct,
  'jeephp2js.userImg_Struct' => $userImg_Struct,
]);

?>

<div id="md_iconSelector" data-modalType="md_iconSelector">
  <ul class="nav nav-tabs" role="tablist" style="/* padding-top:60px; */">
    <?php if (init('showIcon') == 1) { ?>
      <li role="presentation" class="active">
        <a href="#tabicon" role="tab" data-toggle="tab"><i class="fas fa-icons"></i> {{Icônes}}</a>
      </li>
    <?php } ?>
    <?php if (init('showImg') == 1) { ?>
      <li role="presentation" class="<?php if (init('showIcon') == 0) echo 'active' ?>">
        <a href="#tabimg" role="tab" data-toggle="tab"><i class="far fa-images"></i> {{Images}}</a>
      </li>
    <?php } ?>
  </ul>
  <div class="tab-content" style="overflow-y:scroll;max-height: 100%">
    <div id="tabicon" role="tabpanel" class="tab-pane <?php if (init('showIcon') == 1) echo 'active' ?>" <?php if (init('showIcon') != 1) echo ' style="display:none;"' ?>>
      <div class="imgContainer" style="padding-top:10px;">
        <div id="treeFolder-icon" class="div_treeFolder"></div>
        <div class="div_imageGallery"></div>
      </div>
    </div>
    <div id="tabimg" role="tabpanel" class="tab-pane <?php if (init('showIcon') == 0) echo 'active' ?>" <?php if (init('showImg') != 1) echo ' style="display:none;"' ?>>
      <div id="treeFunctions">
        <span class="bt_upload"><i class="fas fa-file-upload" title="{{Ajouter}}"></i></span>
        <span class="bt_new"><i class="fas fa-folder-plus" title="{{Nouveau}}"></i></span>
        <span class="bt_rename"><i class="fas fa-folder" title="{{Renommer}}"></i></span>
        <span class="bt_delete"><i class="fas fa-folder-minus" title="{{Supprimer}}"></i></span>
      </div>
      <input class="hidden" id="bt_uploadImg" type="file" name="file" multiple="multiple" data-path="">
      <div class="imgContainer" style="padding-top: 10px;">
        <div id="treeFolder-img" class="div_treeFolder"></div>
        <div class="div_imageGallery"></div>
      </div>
    </div>
  </div>

  <div id="mySearch" class="input-group">
    <input class="form-control" placeholder="{{Rechercher}}" id="in_searchIconSelector">
    <div class="input-group-btn">
      <a id="bt_resetIconSelectorSearch" class="btn roundedRight" style="width:30px"><i class="fas fa-times"></i> </a>
    </div>
    <div id="bt_cancelConfirm" class="input-group-btn">
    </div>
  </div>
</div>

<?php
include_file('3rdparty', 'tree/treejs', 'css');
include_file('3rdparty', 'tree/tree', 'js');
?>

<script>
  if (!jeeFrontEnd.md_iconSelector) {
    jeeFrontEnd.md_iconSelector = {
      iconClasses: null,
      init: function() {
        this.setModal()
        TreeConfig.leaf_icon = '<i class="far fa-folder cursor"></i>'
        TreeConfig.parent_icon = '<i class="far fa-folder-tree cursor"></i>'
        TreeConfig.open_icon = '<i class="far fa-folder-open cursor"></i>'
        TreeConfig.close_icon = '<i class="far fa-folder cursor"></i>'

        if (Object.keys(jeephp2js.icon_Struct).length > 0) this.setIconTree()
        if (Object.keys(jeephp2js.userImg_Struct).length > 0) this.setUserImgTree()
      },
      postInit: function() {
        if (jeedomUtils.userDevice.type == 'desktop') document.getElementById("in_searchIconSelector").focus();
        if (jeephp2js.showIcon == 1) {
          if (jeephp2js.md_iconSelector_selectIcon != '' && jeephp2js.md_iconSelector_selectIcon != '0') { // Select current icon
            let icon = document.querySelector('#tabicon div.div_imageGallery span.iconSel > svg[data-icon="' + jeephp2js.md_iconSelector_selectIcon + '"]');
            if (icon) icon.closest('div.divIconSel').addClass('iconSelected').scrollIntoView({
              block: "center"
            });
          } else { // Select first icon category
            console.log('showIcon')
            document.querySelector('span.tj_description').click();
          }
        } else {
          document.querySelector('#treeFolder-img span.tj_description')?.click();
        }
      },
      setModal: function() {
        var modal = jeeDialog.get('#md_iconSelector', 'dialog')
        var modalFooter = jeeDialog.get('#md_iconSelector', 'footer')
        var uiOptions = modal.querySelector('#mySearch')
        var btTarget = uiOptions.querySelector('#bt_cancelConfirm')
        modalFooter.insertBefore(uiOptions, modalFooter.firstChild)
        btTarget.append(modalFooter.querySelector('button[data-type="cancel"]'))
        btTarget.append(modalFooter.querySelector('button[data-type="confirm"]'))
        //modal.querySelector('.jeeDialogContent').style.overflowY = 'hidden'
        //document.getElementById('sel_colorIcon').selectedIndex = 1
        modal.querySelector('.jeeDialogContent').style.overflow = 'unset'
      },
      //Tree builders:
      setIconTree: function() {
        this.icon_root = new TreeNode('icons_root', {
          expanded: true
        })
        this.icon_tree = new TreeView(this.icon_root, document.getElementById('treeFolder-icon'), {
          show_root: false
        })
        var newNode
        var tmp = null
        var folderDisplayContainer = document.getElementById('treeFolder-icon').parentNode.querySelector('div.div_imageGallery')
        folderDisplayContainer.empty()
        for (const category in jeephp2js.icon_Struct) {
          const keyClass = 'ico' + category.replace('-', '').replace(' ', '')
          newNode = new TreeNode('<span class="leafRef cursor ' + keyClass + '">' + category + '</span>');
          newNode.on('click', (event, node) => {
            document.querySelector('legend.' + keyClass)?.scrollIntoView();
          });
          this.icon_root.addChild(newNode);

          //document.getElementById('sel_colorIcon').seen()
          //const iconColor = (document.getElementById('sel_colorIcon').value != '') ? (' ' + document.getElementById('sel_colorIcon').value) : '';
          var iconClasses = jeephp2js.md_iconSelector_selectIcon.iconClasses

          const tagB = document.createElement('b');
          tagB.textContent = category;
          const tagLegend = document.createElement('legend');
          tagLegend.className = keyClass;
          tagLegend.appendChild(tagB);
          folderDisplayContainer.appendChild(tagLegend);

          const iconList = jeephp2js.icon_Struct[category]
          for (const i in iconList) {
            var selected = (iconClasses && iconClasses[2] === iconList[i]) ? ' iconSelected' : ''
            var name = Object.values(iconList[i])[0]

            const tagDiv = document.createElement('div');
            tagDiv.className = 'divIconSel cursor text-center ' + keyClass + selected;
            const tagSvg = document.createElement('svg');

            var svgElem = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
            var useElem = document.createElementNS('http://www.w3.org/2000/svg', 'use');
            svgElem.setAttribute('width', '32')
            svgElem.setAttribute('height', '32')
            svgElem.setAttribute('fill', 'var(--txt-color)')
            svgElem.setAttribute('style', 'color:var(--txt-color)')
            svgElem.setAttribute('data-icon', Object.keys(iconList[i])[0]);
            //useElem.setAttributeNS('http://www.w3.org/1999/xlink', 'xlink:href', 'plugins/powerFlow/core/template/dashboard/icon.svg#icon-' + Object.keys(iconList[i])[0]);
            useElem.setAttributeNS('http://www.w3.org/1999/xlink', 'xlink:href', 'plugins/powerFlow/desktop/images/icon.svg#icon-' + Object.keys(iconList[i])[0]);
            useElem.setAttribute('data-icon', Object.keys(iconList[i])[0]);
            svgElem.appendChild(useElem);
            const tagSpan1 = document.createElement('span');
            tagSpan1.className = 'iconSel';
            tagSpan1.appendChild(svgElem);
            tagDiv.appendChild(tagSpan1);
            tagDiv.appendChild(document.createElement('br'));
            const tagSpan2 = document.createElement('span');
            tagSpan2.className = 'iconDesc';
            tagSpan2.textContent = name;
            tagDiv.appendChild(tagSpan2);
            tagDiv.style.display = 'block'

            folderDisplayContainer.appendChild(tagDiv)
          }
        }
        this.icon_tree.reload()
      },
      iconTreeOnScroll: function() {
        var view = document.querySelector('#md_iconSelector .tab-content').getBoundingClientRect();
        var legends = document.querySelectorAll('#tabicon .imgContainer legend');
        var i = 0;
        while (i < legends.length && legends[i].getBoundingClientRect().bottom < view.top) {
          document.querySelector('#treeFolder-icon .' + (legends[i].className)).parentNode.removeClass('selected');
          i += 1;
        }
        if (i < legends.length && legends[i].getBoundingClientRect().bottom < view.bottom) { // In view
          document.querySelector('#treeFolder-icon .' + (legends[i].className)).parentNode.addClass('selected');
          i += 1;
        } else { // Out of view, select last
          document.querySelector('#treeFolder-icon .' + (legends[i - 1].className)).parentNode.addClass('selected');
        }
        while (i < legends.length) {
          document.querySelector('#treeFolder-icon .' + (legends[i].className)).parentNode.removeClass('selected');
          i += 1;
        }
      },
      setUserImgTree: function() {
        this.userImg_root = new TreeNode('<span class="leafRef cursor" data-path="' + jeephp2js.userImg_Struct['rootPath'] + '">Img</span>', {
          expanded: true,
          options: {
            path: jeephp2js.userImg_Struct['rootPath'],
          }
        })
        this.userImg_tree = new TreeView(this.userImg_root, document.getElementById('treeFolder-img'))

        this.userImg_root.on('click', (event, node) => {
          if (!event.target.matches('i')) node.toggleExpanded() //Default behavior always toggle
          jeeFrontEnd.md_iconSelector.onClickUserFolder(node)
        })

        //ContextMenu
        new jeeCtxMenu({
          selector: '#treeFolder-img span.tj_description',
          zIndex: 9999,
          items: {
            upload: {
              name: '{{Ajouter}}',
              icon: 'fas fa-file-upload',
              callback: function(key, opt) {
                jeeFrontEnd.md_iconSelector.uploadToFolder(opt.trigger.tj_node)
              },
            },
            createSubFolder: {
              name: '{{Nouveau}}',
              icon: 'fas fa-folder-plus',
              callback: function(key, opt) {
                jeeFrontEnd.md_iconSelector.createFolder(opt.trigger.tj_node)
              },
            },
            Rename: {
              name: '{{Renommer}}',
              icon: 'fas fa-folder',
              callback: function(key, opt) {
                jeeFrontEnd.md_iconSelector.renameFolder(opt.trigger.tj_node)
              },
            },
            Delete: {
              name: '{{Supprimer}}',
              icon: 'fas fa-folder-minus',
              callback: function(key, opt) {
                jeeFrontEnd.md_iconSelector.deleteFolder(opt.trigger.tj_node)
              },
            }
          }
        })
      },
      onClickUserFolder: function(_node) {
        var path = _node.getOptions().options.path
        jeeFrontEnd.md_iconSelector.printFileFolder(path, 'treeFolder-img')
        //Get subfolders:
        if (_node.getChildren().length > 0) { //Node ever opened, childs loaded.
          return
        }
        jeedom.getFileFolder({
          type: 'folders',
          path: path,
          error: function(error) {
            jeedomUtils.showAlert({
              attachTo: jeeDialog.get('#md_iconSelector', 'dialog'),
              message: error.message,
              level: 'danger'
            })
          },
          success: function(data) {
            for (var i in data) {
              newNode = new TreeNode('<span class="leafRef cursor" data-path="' + path + data[i] + '">' + data[i].replace('/', '') + '</span>', {
                options: {
                  path: path + data[i],
                }
              })
              newNode.on('click', (event, node) => {
                jeeFrontEnd.md_iconSelector.onClickUserFolder(node)
              })
              _node.addChild(newNode)
            }
            jeeFrontEnd.md_iconSelector.userImg_tree.reload()
          }
        })
      },
      uploadToFolder: function(_node) {
        document.getElementById('bt_uploadImg').setAttribute('data-path', _node.getOptions().options.path.replace(/^.+\/..\/..\//, ''))
        document.getElementById('bt_uploadImg').click()
      },
      createFolder: function(_node) {
        var path = _node.getOptions().options.path
        jeeDialog.prompt("{{Nom du nouveau dossier}} ?", function(result) {
          if (result !== null) {
            jeedom.createFolder({
              name: result,
              path: path,
              error: function(error) {
                jeedomUtils.showAlert({
                  attachTo: jeeDialog.get('#md_iconSelector', 'dialog'),
                  message: error.message,
                  level: 'danger'
                })
              },
              success: function() {
                var childs = _node.getChildren()
                for (var i = childs.length - 1; i >= 0; i--) {
                  _node.removeChildPos(i)
                }
                jeeFrontEnd.md_iconSelector.onClickUserFolder(_node)
              }
            })
          }
        })
      },
      renameFolder: function(_node) {
        if (_node == _node.getRoot()) {
          jeedomUtils.showAlert({
            attachTo: jeeDialog.get('#md_iconSelector', 'dialog'),
            message: '{{Impossible de renommer le dossier parent}}',
            level: 'warning'
          })
          return false
        }

        var path = _node.getOptions().options.path
        jeeDialog.prompt("{{Nouveau nom du dossier}} ?", function(result) {
          if (result !== null) {
            var newPath = path.substring(1, path.length - 1)
            newPath = newPath.split('/')
            newPath.pop()
            newPath = '/' + newPath.join('/') + '/' + result + '/'
            jeedom.renameFolder({
              src: path,
              dst: newPath,
              error: function(error) {
                jeedomUtils.showAlert({
                  attachTo: jeeDialog.get('#md_iconSelector', 'dialog'),
                  message: error.message,
                  level: 'danger'
                })
              },
              success: function() {
                var childs = _node.parent.getChildren()
                for (var i = childs.length - 1; i >= 0; i--) {
                  _node.parent.removeChildPos(i)
                }
                _node.parent.setSelected(true)
                jeeFrontEnd.md_iconSelector.onClickUserFolder(_node.parent)
              }
            })
          }
        })
      },
      deleteFolder: function(_node) {
        if (_node == _node.getRoot()) {
          jeedomUtils.showAlert({
            attachTo: jeeDialog.get('#md_iconSelector', 'dialog'),
            message: '{{Impossible de supprimer le dossier parent}}',
            level: 'warning'
          })
          return false
        }

        var path = _node.getOptions().options.path
        var msg = "{{Etes-vous sûr de vouloir supprimer le dossier}} <strong>" + path.replace(/^.+\/..\/..\//, '') + "</strong> ?<br>{{Attention : le contenu du dossier sera définitivement supprimé lors de l'opération.}}"
        jeeDialog.confirm(msg, function(result) {
          if (result !== null) {
            jeedom.deleteFolder({
              path: path,
              error: function(error) {
                jeedomUtils.showAlert({
                  attachTo: jeeDialog.get('#md_iconSelector', 'dialog'),
                  message: error.message,
                  level: 'danger'
                })
              },
              success: function() {
                _node.parent.removeChild(_node)
                jeeFrontEnd.md_iconSelector.userImg_tree.getSelectedNodes().forEach(_sel => {
                  _sel.setSelected(false)
                })
                _node.parent.setSelected(true)
                jeeFrontEnd.md_iconSelector.userImg_tree.reload()
                jeeFrontEnd.md_iconSelector.onClickUserFolder(_node.parent)
              }
            })
          }
        })
      },
      capitalizeFirstLetter: function(_string) {
        return _string.charAt(0).toUpperCase() + _string.slice(1)
      },
      //Load selected tree into container:
      printFileFolder: function(_path, jstreeId, callback) {
        jeedomUtils.hideAlert()
        jeedom.getFileFolder({
          type: 'files',
          path: _path,
          error: function(error) {
            jeedomUtils.showAlert({
              attachTo: jeeDialog.get('#md_iconSelector', 'dialog'),
              message: error.message,
              level: 'danger'
            })
          },
          success: function(data) {
            var folderDisplayContainer = document.getElementById(jstreeId).parentNode.querySelector('div.div_imageGallery')
            folderDisplayContainer.empty()
            var realPath = _path.replace(/^.+\/..\/..\//, '')
            var div = ''
            if (jstreeId === 'treeFolder-img') {
              //document.getElementById('sel_colorIcon').unseen()
              for (var i in data) {
                div += '<div class="divIconSel divImgSel">'
                div += '<div class="cursor iconSel"><img class="img-responsive" src="' + realPath + data[i] + '" data-realfilepath="' + realPath + data[i] + '"/></div>'
                div += '<div class="iconDesc">' + jeeFrontEnd.md_iconSelector.capitalizeFirstLetter(data[i].substr(0, data[i].lastIndexOf('.')).substr(0, 15).split('_').join(' ')) + '</div>'
                div += '<a class="btn btn-danger btn-xs bt_removeImg" data-realfilepath="' + realPath + data[i] + '" style="z-index: 2000;"><i class="fas fa-trash-alt"></i> {{Supprimer}}</a>'
                div += '</div>'
              }
            } else if (jstreeId === 'treeFolder-bg') {
              //document.getElementById('sel_colorIcon').unseen()
              for (var i in data) {
                div += '<div class="divIconSel divBgSel">'
                div += '<div class="cursor iconSel"><img class="img-responsive" src="' + realPath + data[i] + '" data-filename="' + _path + data[i] + '"/></div>'
                div += '<div class="iconDesc">' + jeeFrontEnd.md_iconSelector.capitalizeFirstLetter(data[i].substr(0, data[i].lastIndexOf('.')).split('_').join(' ')) + '</div>'
                div += '</div>'
              }
            }
            folderDisplayContainer.insertAdjacentHTML('beforeend', div)
          }
        })
      }
    }
  }

  (function() { // Self Isolation!
    jeeFrontEnd.md_iconSelector.init()

    //Manage events outside parents delegations:
    document.getElementById('in_searchIconSelector').addEventListener('keyup', function(event) {
      var tab = document.querySelector('#md_iconSelector div.tab-pane.active').getAttribute('id')

      document.querySelectorAll('.divIconSel').seen()
      var search = event.target.value
      if (search != '') {
        search = jeedomUtils.normTextLower(search)
        document.querySelectorAll('#' + tab + ' .iconDesc').forEach(_item => {
          if (!jeedomUtils.normTextLower(_item.textContent).includes(search)) {
            _item.closest('.divIconSel').unseen()
          }
        })
      }
      document.querySelectorAll('#' + tab + ' .imgContainer legend').forEach(_item => {
        var k = _item.nextSibling;
        while (k != null && !k.isVisible() && k.tagName == 'DIV') {
          k = k.nextSibling;
        }
        (k == null || k.tagName == 'LEGEND') ? _item.unseen(): _item.seen();
      });
      jeeFrontEnd.md_iconSelector.iconTreeOnScroll();
    })
    document.getElementById('bt_resetIconSelectorSearch').addEventListener('click', function(event) {
      document.getElementById('in_searchIconSelector').jeeValue('').triggerEvent('keyup')
    })

    /*Events delegations
     */
    document.querySelector('#md_iconSelector .tab-content').addEventListener("scroll", jeeFrontEnd.md_iconSelector.iconTreeOnScroll);

    document.getElementById('md_iconSelector').addEventListener('click', function(event) {
      var _target = null
      if (_target = event.target.closest('a.bt_removeImg')) {
        jeedomUtils.hideAlert()
        var filepath = _target.getAttribute('data-realfilepath')
        jeeDialog.confirm('{{Êtes-vous sûr de vouloir supprimer cette image}} <strong>' + filepath + '</strong> ?', function(result) {
          if (result) {
            jeedom.removeImageIcon({
              filepath: filepath,
              error: function(error) {
                jeedomUtils.showAlert({
                  attachTo: jeeDialog.get('#md_iconSelector', 'dialog'),
                  message: error.message,
                  level: 'danger'
                })
              },
              success: function() {
                document.querySelector('.leafRef[data-path$="' + filepath.replace(/[^\/]+$/, '') + '"]')?.click()
              }
            })
          }
        })
        return
      }

      if (_target = event.target.closest('.divIconSel')) {
        document.querySelectorAll('.divIconSel').removeClass('iconSelected')
        _target.closest('.divIconSel').addClass('iconSelected')
        return
      }

      if (_target = event.target.closest('#mod_selectIcon ul.nav.nav-tabs li a')) {
        jeedomUtils.hideAlert()
        var tabhref = _target.getAttribute('href')
        if (tabhref === '#tabimg') {
          if (!document.querySelector(tabhref + ' .div_treeFolder .tj_description.selected')) {
            document.querySelector(tabhref + ' .div_treeFolder span.tj_description').click()
          }
        }
        return
      }

      if (_target = event.target.closest('#treeFunctions .bt_upload')) {
        jeeFrontEnd.md_iconSelector.uploadToFolder(jeeFrontEnd.md_iconSelector.userImg_tree.getSelectedNodes()[0])
        return
      }

      if (_target = event.target.closest('#treeFunctions .bt_new')) {
        jeeFrontEnd.md_iconSelector.createFolder(jeeFrontEnd.md_iconSelector.userImg_tree.getSelectedNodes()[0])
        return
      }

      if (_target = event.target.closest('#treeFunctions .bt_rename')) {
        jeeFrontEnd.md_iconSelector.renameFolder(jeeFrontEnd.md_iconSelector.userImg_tree.getSelectedNodes()[0])
        return
      }

      if (_target = event.target.closest('#treeFunctions .bt_delete')) {
        jeeFrontEnd.md_iconSelector.deleteFolder(jeeFrontEnd.md_iconSelector.userImg_tree.getSelectedNodes()[0])
        return
      }
    })

    document.getElementById('md_iconSelector').addEventListener('dblclick', function(event) {
      var _target = null
      if (_target = event.target.closest('.divIconSel')) {
        document.querySelectorAll('.divIconSel').removeClass('iconSelected')
        _target.closest('.divIconSel').addClass('iconSelected')
        document.getElementById('mod_selectIcon').querySelector('button[data-type="confirm"]').click()
        return
      }
    })

    if (jeephp2js.showImg == 1) {
      new jeeFileUploader({
        fileInput: document.getElementById('bt_uploadImg'),
        add: function(event, options) {
          let currentPath = document.getElementById('bt_uploadImg').getAttribute('data-path')
          options.url = 'core/ajax/jeedom.ajax.php?action=uploadImageIcon&filepath=' + currentPath
          options.submit()
        },
        done: function(event, data) {
          if (data.result.state != 'ok') {
            jeedomUtils.showAlert({
              attachTo: jeeDialog.get('#md_iconSelector', 'dialog'),
              message: data.result.result,
              level: 'danger'
            })
            return
          }
          document.querySelector('.leafRef[data-path$="' + data.result.result.filepath.replace(/^.+\/..\/..\//, '').replace(/[^\/]+$/, '') + '"]')?.click()
        }
      })
    }

    jeeFrontEnd.md_iconSelector.postInit()

  })()
</script>