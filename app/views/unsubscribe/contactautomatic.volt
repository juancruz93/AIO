{% extends "templates/clean.volt" %}
{% block header %}
  {# Start Notifications #}
  {{ partial("partials/css_notifications_partial") }}
  {# End Notifications #}
{% endblock %}
{% block css %}
  {{ stylesheet_link('library/angular-material-1.1.0/css/angular-material.min.css') }}
  {{ stylesheet_link('library/ui-select-master/dist/select.css') }}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/3.4.5/select2.css">    
{% endblock %}

{% block js %}
  <script>
    var idMail = {{idMail}};
      var idContact = {{idContact}};
      var relativeUrlBase = "{{ urlManager.get_base_uri() }}";
    var fullUrlBase = "{{ urlManager.get_base_uri(true) }}";
    var templateBase = "unsubscribe";
  </script>
  <script>
    (function ()
    {

      //exclude older browsers by the features we need them to support
      //and legacy opera explicitly so we don't waste time on a dead browser
      if
        (
          !document.querySelectorAll
          ||
          !('draggable' in document.createElement('span'))
          ||
          window.opera
          )
      {
        return;
      }

      //get the collection of draggable targets and add their draggable attribute
      for (var
        targets = document.querySelectorAll('[data-draggable="target"]'),
        len = targets.length,
        i = 0; i < len; i++)
      {
        targets[i].setAttribute('aria-dropeffect', 'none');
      }

      //get the collection of draggable items and add their draggable attributes
      for (var
        items = document.querySelectorAll('[data-draggable="item"]'),
        len = items.length,
        i = 0; i < len; i++)
      {
        items[i].setAttribute('draggable', 'true');
        items[i].setAttribute('aria-grabbed', 'false');
        items[i].setAttribute('tabindex', '0');
      }



      //dictionary for storing the selections data 
      //comprising an array of the currently selected items 
      //a reference to the selected items' owning container
      //and a refernce to the current drop target container
      var selections =
        {
          items: [],
          owner: null,
          droptarget: null
        };

      //function for selecting an item
      function addSelection(item)
      {
        //if the owner reference is still null, set it to this item's parent
        //so that further selection is only allowed within the same container
        if (!selections.owner)
        {
          selections.owner = item.parentNode;
        }

        //or if that's already happened then compare it with this item's parent
        //and if they're not the same container, return to prevent selection
        else if (selections.owner != item.parentNode)
        {
          return;
        }

        //set this item's grabbed state
        item.setAttribute('aria-grabbed', 'true');

        //add it to the items array
        selections.items.push(item);

      }

      //function for unselecting an item
      function removeSelection(item)
      {
        //reset this item's grabbed state
        item.setAttribute('aria-grabbed', 'false');

        //then find and remove this item from the existing items array
        for (var len = selections.items.length, i = 0; i < len; i++)
        {
          if (selections.items[i] == item)
          {
            selections.items.splice(i, 1);
            break;
          }
        }

      }

      //function for resetting all selections
      function clearSelections()
      {
        //if we have any selected items
        if (selections.items.length)
        {
          //reset the owner reference
          selections.owner = null;

          //reset the grabbed state on every selected item
          for (var len = selections.items.length, i = 0; i < len; i++)
          {
            selections.items[i].setAttribute('aria-grabbed', 'false');
          }

          //then reset the items array        
          selections.items = [];
        }
      }

      //shorctut function for testing whether a selection modifier is pressed
      function hasModifier(e)
      {
        return (e.ctrlKey || e.metaKey || e.shiftKey);
      }


      //function for applying dropeffect to the target containers
      function addDropeffects()
      {
        //apply aria-dropeffect and tabindex to all targets apart from the owner
        for (var len = targets.length, i = 0; i < len; i++)
        {
          if
            (
              targets[i] != selections.owner
              &&
              targets[i].getAttribute('aria-dropeffect') == 'none'
              )
          {
            targets[i].setAttribute('aria-dropeffect', 'move');
            targets[i].setAttribute('tabindex', '0');
          }
        }

        //remove aria-grabbed and tabindex from all items inside those containers
        for (var len = items.length, i = 0; i < len; i++)
        {
          if
            (
              items[i].parentNode != selections.owner
              &&
              items[i].getAttribute('aria-grabbed')
              )
          {
            items[i].removeAttribute('aria-grabbed');
            items[i].removeAttribute('tabindex');
          }
        }
      }

      //function for removing dropeffect from the target containers
      function clearDropeffects()
      {
        //if we have any selected items
        if (selections.items.length)
        {
          //reset aria-dropeffect and remove tabindex from all targets
          for (var len = targets.length, i = 0; i < len; i++)
          {
            if (targets[i].getAttribute('aria-dropeffect') != 'none')
            {
              targets[i].setAttribute('aria-dropeffect', 'none');
              targets[i].removeAttribute('tabindex');
            }
          }

          //restore aria-grabbed and tabindex to all selectable items 
          //without changing the grabbed value of any existing selected items
          for (var len = items.length, i = 0; i < len; i++)
          {
            if (!items[i].getAttribute('aria-grabbed'))
            {
              items[i].setAttribute('aria-grabbed', 'false');
              items[i].setAttribute('tabindex', '0');
            } else if (items[i].getAttribute('aria-grabbed') == 'true')
            {
              items[i].setAttribute('tabindex', '0');
            }
          }
        }
      }

      //shortcut function for identifying an event element's target container
      function getContainer(element)
      {
        do
        {
          if (element.nodeType == 1 && element.getAttribute('aria-dropeffect'))
          {
            return element;
          }
        } while (element = element.parentNode);

        return null;
      }



      //mousedown event to implement single selection
      document.addEventListener('mousedown', function (e)
      {

        //if the element is a draggable item
        if (e.target.getAttribute('draggable'))
        {
          //clear dropeffect from the target containers
          clearDropeffects();

          //if the multiple selection modifier is not pressed 
          //and the item's grabbed state is currently false
          if
            (
              !hasModifier(e)
              &&
              e.target.getAttribute('aria-grabbed') == 'false'
              )
          {
            //clear all existing selections
            clearSelections();

            //then add this new selection
            addSelection(e.target);
          }
        }

        //else [if the element is anything else]
        //and the selection modifier is not pressed 
        else if (!hasModifier(e))
        {
          //clear dropeffect from the target containers
          clearDropeffects();

          //clear all existing selections
          clearSelections();
        }

        //else [if the element is anything else and the modifier is pressed]
        else
        {
          //clear dropeffect from the target containers
          clearDropeffects();
        }

      }, false);

      //mouseup event to implement multiple selection
      document.addEventListener('mouseup', function (e)
      {
        //if the element is a draggable item 
        //and the multipler selection modifier is pressed
        if (e.target.getAttribute('draggable') && hasModifier(e))
        {
          //if the item's grabbed state is currently true
          if (e.target.getAttribute('aria-grabbed') == 'true')
          {
            //unselect this item
            removeSelection(e.target);

            //if that was the only selected item
            //then reset the owner container reference
            if (!selections.items.length)
            {
              selections.owner = null;
            }
          }

          //else [if the item's grabbed state is false]
          else
          {
            //add this additional selection
            addSelection(e.target);
          }
        }

      }, false);

      //dragstart event to initiate mouse dragging
      document.addEventListener('dragstart', function (e)
      {
        //if the element's parent is not the owner, then block this event
        if (selections.owner != e.target.parentNode)
        {
          e.preventDefault();
          return;
        }

        //[else] if the multiple selection modifier is pressed 
        //and the item's grabbed state is currently false
        if
          (
            hasModifier(e)
            &&
            e.target.getAttribute('aria-grabbed') == 'false'
            )
        {
          //add this additional selection
          addSelection(e.target);
        }

        //we don't need the transfer data, but we have to define something
        //otherwise the drop action won't work at all in firefox
        //most browsers support the proper mime-type syntax, eg. "text/plain"
        //but we have to use this incorrect syntax for the benefit of IE10+
        e.dataTransfer.setData('text', '');

        //apply dropeffect to the target containers
        addDropeffects();

      }, false);



      //keydown event to implement selection and abort
      document.addEventListener('keydown', function (e)
      {
        //if the element is a grabbable item 
        if (e.target.getAttribute('aria-grabbed'))
        {
          //Space is the selection or unselection keystroke
          if (e.keyCode == 32)
          {
            //if the multiple selection modifier is pressed 
            if (hasModifier(e))
            {
              //if the item's grabbed state is currently true
              if (e.target.getAttribute('aria-grabbed') == 'true')
              {
                //if this is the only selected item, clear dropeffect 
                //from the target containers, which we must do first
                //in case subsequent unselection sets owner to null
                if (selections.items.length == 1)
                {
                  clearDropeffects();
                }

                //unselect this item
                removeSelection(e.target);

                //if we have any selections
                //apply dropeffect to the target containers, 
                //in case earlier selections were made by mouse
                if (selections.items.length)
                {
                  addDropeffects();
                }

                //if that was the only selected item
                //then reset the owner container reference
                if (!selections.items.length)
                {
                  selections.owner = null;
                }
              }

              //else [if its grabbed state is currently false]
              else
              {
                //add this additional selection
                addSelection(e.target);

                //apply dropeffect to the target containers    
                addDropeffects();
              }
            }

            //else [if the multiple selection modifier is not pressed]
            //and the item's grabbed state is currently false
            else if (e.target.getAttribute('aria-grabbed') == 'false')
            {
              //clear dropeffect from the target containers
              clearDropeffects();

              //clear all existing selections
              clearSelections();

              //add this new selection
              addSelection(e.target);

              //apply dropeffect to the target containers
              addDropeffects();
            }

            //else [if modifier is not pressed and grabbed is already true]
            else
            {
              //apply dropeffect to the target containers    
              addDropeffects();
            }

            //then prevent default to avoid any conflict with native actions
            e.preventDefault();
          }

          //Modifier + M is the end-of-selection keystroke
          if (e.keyCode == 77 && hasModifier(e))
          {
            //if we have any selected items
            if (selections.items.length)
            {
              //apply dropeffect to the target containers    
              //in case earlier selections were made by mouse
              addDropeffects();

              //if the owner container is the last one, focus the first one
              if (selections.owner == targets[targets.length - 1])
              {
                targets[0].focus();
              }

              //else [if it's not the last one], find and focus the next one
              else
              {
                for (var len = targets.length, i = 0; i < len; i++)
                {
                  if (selections.owner == targets[i])
                  {
                    targets[i + 1].focus();
                    break;
                  }
                }
              }
            }

            //then prevent default to avoid any conflict with native actions
            e.preventDefault();
          }
        }

        //Escape is the abort keystroke (for any target element)
        if (e.keyCode == 27)
        {
          //if we have any selected items
          if (selections.items.length)
          {
            //clear dropeffect from the target containers
            clearDropeffects();

            //then set focus back on the last item that was selected, which is 
            //necessary because we've removed tabindex from the current focus
            selections.items[selections.items.length - 1].focus();

            //clear all existing selections
            clearSelections();

            //but don't prevent default so that native actions can still occur
          }
        }

      }, false);



      //related variable is needed to maintain a reference to the 
      //dragleave's relatedTarget, since it doesn't have e.relatedTarget
      var related = null;

      //dragenter event to set that variable
      document.addEventListener('dragenter', function (e)
      {
        related = e.target;

      }, false);

      //dragleave event to maintain target highlighting using that variable
      document.addEventListener('dragleave', function (e)
      {
        //get a drop target reference from the relatedTarget
        var droptarget = getContainer(related);

        //if the target is the owner then it's not a valid drop target
        if (droptarget == selections.owner)
        {
          droptarget = null;
        }

        //if the drop target is different from the last stored reference
        //(or we have one of those references but not the other one)
        if (droptarget != selections.droptarget)
        {
          //if we have a saved reference, clear its existing dragover class
          if (selections.droptarget)
          {
            selections.droptarget.className =
              selections.droptarget.className.replace(/ dragover/g, '');
          }

          //apply the dragover class to the new drop target reference
          if (droptarget)
          {
            droptarget.className += ' dragover';
          }

          //then save that reference for next time
          selections.droptarget = droptarget;
        }

      }, false);

      //dragover event to allow the drag by preventing its default
      document.addEventListener('dragover', function (e)
      {
        //if we have any selected items, allow them to be dragged
        if (selections.items.length)
        {
          e.preventDefault();
        }

      }, false);



      //dragend event to implement items being validly dropped into targets,
      //or invalidly dropped elsewhere, and to clean-up the interface either way
      document.addEventListener('dragend', function (e)
      {
        //if we have a valid drop target reference
        //(which implies that we have some selected items)
        if (selections.droptarget)
        {
          //append the selected items to the end of the target container
          for (var len = selections.items.length, i = 0; i < len; i++)
          {
            selections.droptarget.appendChild(selections.items[i]);
          }

          //prevent default to allow the action            
          e.preventDefault();
        }

        //if we have any selected items
        if (selections.items.length)
        {
          //clear dropeffect from the target containers
          clearDropeffects();

          //if we have a valid drop target reference
          if (selections.droptarget)
          {
            //reset the selections array
            clearSelections();

            //reset the target's dragover class
            selections.droptarget.className =
              selections.droptarget.className.replace(/ dragover/g, '');

            //reset the target reference
            selections.droptarget = null;
          }
        }

      }, false);



      //keydown event to implement items being dropped into targets
      document.addEventListener('keydown', function (e)
      {
        //if the element is a drop target container
        if (e.target.getAttribute('aria-dropeffect'))
        {
          //Enter or Modifier + M is the drop keystroke
          if (e.keyCode == 13 || (e.keyCode == 77 && hasModifier(e)))
          {
            //append the selected items to the end of the target container
            for (var len = selections.items.length, i = 0; i < len; i++)
            {
              e.target.appendChild(selections.items[i]);
            }

            //clear dropeffect from the target containers
            clearDropeffects();

            //then set focus back on the last item that was selected, which is 
            //necessary because we've removed tabindex from the current focus
            selections.items[selections.items.length - 1].focus();

            //reset the selections array
            clearSelections();

            //prevent default to to avoid any conflict with native actions
            e.preventDefault();
          }
        }

      }, false);

    })();

  </script>
  {{ javascript_include('library/angular-1.5/js/angular.min.js') }}
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-sanitize.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-animate.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-aria.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angular_material/1.0.7/angular-material.min.js"></script>

  {{javascript_include('js/angular/unsubscribe/services.js') }}
  {{ partial("partials/js_notifications_partial") }}
  {{ partial("partials/slideontop_notification_partial") }}
  {{javascript_include('js/angular/unsubscribe/controllers.js') }}

  {{ javascript_include('library/ui-select-0.19.6/select.min.js') }}
  {{ javascript_include('library/angular-dragdrop/component/jquery-ui/jquery-ui.js')}}
  {{ javascript_include('library/angular-dragdrop/src/angular-dragdrop.js')}}

{% endblock %}

{% block content %}
  <div class="clearfix"></div>
  <div class="site-wrapper" ng-controller="contactautomaticController" ng-cloak>
    <div class="site-wrapper-inner">
      <div class="center-container">
        <div class="session-container">
          <img class="session-logo" style="width: 100px" src="{{url('')}}themes/{{theme.name}}/images/aio.png" />
          <div class="space">
            <div class="clearfix">
              <div>
                <p class="small-text contactlist-categories text-left">Hola <strong>{{'{{name}}'}}</strong>,
                  {#                  <br>¿Estas seguro que quieres desuscribirte?#}
                </p>
                <p class="small-text contactlist-categories text-left">
                  Puede arrastrar las categorías de un lado a otro para inscribirlas o desuscribirlas</p>
                <br>
                <div>
                  <br>
                  <div class="contactlist-categories row">
                    <div class="col-lg-6  panel-sigma">
                      <div class="panel-heading">Categorías en las que está inscrito</div>
                      <div id="subs" style="background-color: #B5B5B8;" class="panel-body border-category  boder-droppable " data-drop="true" ng-model='arrSubs' data-jqyoui-options="actionDroppableSubs"  jqyoui-droppable="{multiple:true,onOver:'actionDroppableSubs.onOver',onDrop:'actionDroppableSubs.onDrop',onOut:'actionDroppableSubs.onDrop'}">
                        <ol class="" style="padding: 0px;" >
                          <li data-draggable="item" class="item-collap cursor-move " ng-repeat="category in arrSubs track by $index" data-drag="true" data-jqyoui-options="{revert: 'invalid'}" ng-model="arrSubs" jqyoui-draggable="{index: {{'{{$index}}'}},placeholder:true,animate:true}"  ng-hide="!category.name">
                            {{"{{category.name}}"}}
                            <i class="fa fa-thumbs-down float-right cursor-pointer" style="margin-top: 5px; color: #f51818;" aria-hidden="true" title="Desuscribir" ng-click="unsubscribe(category)"></i>
                          </li>
                        </ol>
                      </div>
                    </div>
                    <div class="col-lg-6  panel-sigma">
                      <div class="panel-heading">Categorías en las que está desuscrito</div>
                      <div id="unsubs" style="background-color: #B5B5B8;" class="panel-body border-category boder-droppable " data-drop="true" ng-model="arrUnsubs" data-jqyoui-options="actionDroppableUnSubs" jqyoui-droppable="{multiple:true,onOver:'actionDroppableUnSubs.onOver',onDrop:'actionDroppableUnSubs.onDrop',onOut:'actionDroppableUnSubs.onDrop'}">
                        <ol style="padding: 0px;" >
                          <li data-draggable="item" class="item-collap cursor-move" ng-repeat="category in arrUnsubs track by $index" data-drag="true" data-jqyoui-options="{revert: 'invalid'}" ng-model="arrUnsubs" jqyoui-draggable="{index: {{'{{$index}}'}},placeholder:true,animate:true}" ng-hide="!category.name">
                            {{"{{category.name}}"}}
                            <i class="fa fa-thumbs-up float-right cursor-pointer" style="margin-top: 5px; color: #5cbd56;" aria-hidden="true" title="Suscribir" ng-click="subscribe(category)"></i>
                          </li>
                        </ol>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="contactlist-categories text-right">
                  <button class="button btn primary-inverted " data-ng-click="sendUnsubscribe()">Confirmar Des susripción</button>
                </div>
              </div>
              <br>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <!-- DRAGGABLE -->
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    {# <div class="site-wrapper-inner">
       <div class="center-container">
         <div class="session-container">
           <img class="session-logo" style="width: 100px" src="{{url('')}}themes/{{theme.name}}/images/aio.png" />
           <div class="space">
             <div class="clearfix">
               <div><p class="small-text">Hola <strong>{{'{{name}}'}}</strong><br>¿Estas seguro que quieres desuscribirte?</p></div>
 
               <table class="table table-bordered">
                 <thead class="theader">
                   <tr>
                     <th>Nombre de la lista</th>
                     <th></th>
                   </tr>
                 </thead>
                 <tbody>
                   <tr ng-repeat="contactlist in contact.contactlist" >
                     <td>
                       <div class="smaill-text">
                         {{ '{{contactlist.name}}' }}
                       </div>    
                     </td>
                     <td>
                       <div class="pull-right">
                         <md-switch class="md-warn none-margin" ng-model="sendSelected[$index].unsubscribe">
                         </md-switch>
                       </div>
                     </td>
                   </tr>
                 </tbody>
               </table>         
               <div class="pull-right">
                 <button class="button btn primary-inverted" ng-click="sendUnsubscribe()">Confirmar</button>
               </div>          
             </div>
           </div>
         </div>
       </div>
     </div>#}
  </div>
  <div class="principal-menu per-footerColor per-bottomLine">
    <img class="session-logo float-right" style="width: 100px; margin-top: 10px; margin-right: 30px;" src="{{url('')}}themes/default/images/logo.png" />
  </div>
{% endblock %}