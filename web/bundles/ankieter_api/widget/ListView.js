/**
 *  ListView
 *
 *  @param options = {
 *      viewTemplate = string_id
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
 /*  @item_template
    <!-- BEGIN: Underscore Template Definition. -->
    <script type="text/template" id="subscriber_list_template">
        <% _.each( rc.listItems, function( listItem ){ %>
            <li id='player-listItem-<%- listItem.user_id %>' >
            </li>
        <% }); %>
    </script>
    <!-- END: Underscore Template Definition. -->
*/

var VIEW_TYPE = {
    ITEM : "item",
    LOADER : "loader"
};

jQuery.fn.extend({

    ListView : function( options ) {

        _.templateSettings.variable = "rc";

        var model = {

            listView : null,
            viewTemplate : null,
            listItems : [],

            onChange : function(items){},

            init : function(opt,view){

                this.viewTemplate = _.template(
                    $( "#" + opt.viewTemplate ).html()
                );

                this.listView = view;

                if(opt.onChange){
                    this.onChange = opt.onChange;
                }
                this.displayList();
            },

            setData : function( dataList ){
                this.listItems = [];
                for( x in dataList ){
                    this.listItems.push({
                        viewType : VIEW_TYPE.ITEM,
                        id : x,
                        data : dataList[x]
                    });
                }
                this.displayList();
            },

            displayList : function(){
                var sender = this;

                var templateData = {
                    listItems: sender.listItems
                };

                var s = this.viewTemplate( templateData );
                this.listView.html( s );


                this.listView.find("a").click(function() {
                    $(this).toggleClass("active");
                    sender.onChange( sender.getSelectedItems() )
                });

            },

            getSelectedItems : function(){
                var list = {};

                this.listView.find( "a.active").each(function( index ) {
                    list[$( this ).attr("data-id")] = $( this ).attr("data-name");
                });

                return list;
            },

            setToggleItem : function( id ){
                this.listView.find( "a[data-id="+id+"]").toggleClass("active");
            }

        }

        model.init(options,this);
        return model;
    }

});