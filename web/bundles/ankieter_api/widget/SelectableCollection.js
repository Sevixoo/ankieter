/**
 *  SelectableCollection
 *
 *  @param options = {
 *      onChange = function( selected_items[] );
 *  }
 *  @template
 *    <ul class="collection">
 *       <a class="collection-item" data-id="[id]" data-name="[name]"  >
 *           <span >[name]</span>
 *           <span class="badge">[count]</span>
 *       </a>
 *    </ul>
 */
jQuery.fn.extend({

    SelectableCollection : function( options ) {

        var model = {

            view : null,
            onChange : function(items){},

            init : function(opt,view){
                var sender = this;
                this.view = view;
                if(opt.onChange){
                    this.onChange = opt.onChange;
                }

                this.view.find("a").click(function() {
                    $(this).toggleClass("active");
                    sender.onChange( sender.getSelectedItems() )
                });
            },

            getSelectedItems : function(){
                var list = {};

                this.view.find( "a.active").each(function( index ) {
                    list[$( this ).attr("data-id")] = $( this ).attr("data-name");
                });

                return list;
            },

            setToggleItem : function( id ){
                this.view.find( "a[data-id="+id+"]").toggleClass("active");
            }

        }

        model.init(options,this);
        return model;
    }

});