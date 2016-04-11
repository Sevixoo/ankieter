/**
 *  SelectableCollection
 *
 *  @template
 *    <ul class="collection">
 *       <a href="#" class="collection-item" data-id="[id]" data-name="[name]"  >
 *            [name]
 *           <span class="badge">[count]</span>
 *       </a>
 *    </ul>
 */
jQuery.fn.extend({

    SelectableCollection : function( options ) {

        var model = {

            init : function(opt){

            },

            getSelectedItems : function(){

            },

            setSelectedItem : function( id , selected){

            }

        }

        model.init(options);

        return model;
    }

});