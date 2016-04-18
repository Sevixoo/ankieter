/**
 *  SelectableCollection
 *
 *  @param options = {
 *      onChange = function( selected_items[] );
 *      onSelect = function( selected_item );
 *      disallowMultiSelect = true
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
            onSelect : function(item){},
            isMultiSelect : true,

            init : function(opt,view){
                var sender = this;
                this.view = view;
                if(opt.onChange){
                    this.onChange = opt.onChange;
                }

                if(opt.onSelect){
                    this.onSelect = opt.onSelect;
                }

                if(opt.disallowMultiSelect){
                    this.isMultiSelect = !opt.disallowMultiSelect;
                }

                this.view.find("a").click(function() {
                    if(!sender.isMultiSelect) {
                        sender.clearSelection();
                    }
                    $(this).toggleClass("active");

                    sender.onSelect( {
                        id: $( this ).attr("data-id"),
                        name : $( this ).attr("data-name")
                    } );
                    sender.onChange( sender.getSelectedItems() );
                });
            },

            getSelectedItems : function(){
                var list = {};

                this.view.find( "a.active").each(function( index ) {
                    list[$( this ).attr("data-id")] = $( this ).attr("data-name");
                });

                return list;
            },

            getSelectedItem : function(){
                if(this.isMultiSelect){
                    return;
                }
                var data = {};

                this.view.find( "a.active").each(function( index ) {
                    data = {
                       id : $( this ).attr("data-id"),
                       name :  $( this ).attr("data-name")
                    };
                });

                return data;
            },

            setToggleItem : function( id ){
                if(!this.isMultiSelect) {
                    this.clearSelection();
                }
                this.view.find( "a[data-id="+id+"]").toggleClass("active");

            },

            clearSelection : function(){
                this.view.find( "a.active").removeClass("active");
            },

            deleteFromList : function( ids ){
                for(x in ids){
                    this.view.find( "a[data-id="+ids[x]+"]").remove();
                }
                this.onSelect( null );
                this.clearSelection();
            },

            addGroup : function( group ){
                this.view.append(
                    '<a class="collection-item" data-id="'+group.id+'" data-name="'+group.name+'" style="padding: 5px;cursor: pointer;" >' +
                        '<span >'+group.name+'</span>' +
                        '<span class="badge">'+group.size+'</span>' +
                    '</a>'
                );
                var sender = this;
                this.view.find("a[data-id="+group.id+"]").click(function() {
                    if(!sender.isMultiSelect) {
                        sender.clearSelection();
                    }
                    $(this).toggleClass("active");

                    sender.onSelect( {
                        id: $( this ).attr("data-id"),
                        name : $( this ).attr("data-name")
                    } );
                    sender.onChange( sender.getSelectedItems() );
                });

            },

            setGroupSizes : function( groups ){
                for(x in groups){
                    this.view.find("a[data-id="+groups[x].id+"] > .badge").html(groups[x].size);
                }
            }



        }

        model.init(options,this);
        return model;
    }

});