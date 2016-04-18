var AnkieterApi = {

    context : null,

    init : function(){
        this.context = this;

    },
    showLoader : function(){
        $( "#progressLoader" ).show();
    },
    hideLoader : function(){
        $( "#progressLoader" ).hide( "slow" );
    },
    parametrizeUrl : function( data , url ){
        var str = url;
        for( x in data ){
            str = str.replace( "{"+x+"}" , data[x] );
        }
        if( str.search(/{*}/i) >= 0 ){
            throw "Invalid arg in "+str;
        }
        return str;
    },
    REST : {
        post : function( config ){
            var req = new this.request("POST", config );
            return req;
        },
        get : function( config ){
            var req = new this.request("GET", config );
            return req;
        },
        put : function( config ){
            var req = new this.request("PUT", config );
            return req;
        },
        delete : function( config ){
            var req = new this.request("DELETE", config );
            return req;
        },
        /**
         *  request
         *  @param method POST / GET / DELETE
         *  @param config = {
         *      *"url":url String,
         *      "url_data":url_data(key-pairs),
         *      "data":data(key-pairs),
         *      "headers":headers(key-pairs),
         *      "onResult":onResult(Obj data)
         *      "onError":onError(String msg)
         *      "defLoader":defLoader(Boolean)
         *      "loaderView":loaderView(String selector)
         *      "loaderModal":loaderView(String selector)
         *  }
         *  , url_data , form_data , headers , onResult , onError
         * */
        request : function( method , config ){
            if(!config.url) {
                console.error("url == null!");
                return;
            }
            var uri = config.url;
            var defLoader = true;
            var loaderView = false;
            var loaderModal = false;

            if(config.defLoader){
                defLoader = config.defLoader;
            }
            if(config.loaderView){
                loaderView = config.loaderView;
            }
            if(config.loaderModal){
                loaderModal = config.loaderModal;
            }

            if(config.url_data){
                uri = AnkieterApi.parametrizeUrl(  config.url_data , config.url  );
            }

            console.log( "{"+method+"}" + uri );

            var headers = {'Content-Type':'application/json'};

            if(method!="GET"){
                headers = {'Content-Type':'application/x-www-form-urlencoded'};
            }

            if( config.headers ){
                headers =  config.headers;
            }

            for (x in headers) {
                console.log("{header}" + x + "-" + headers[x]);
            }

            var data = {};

            if( config.data ) {
                data = config.data;
                console.log(":");
                for (x in config.data) {
                    console.log("{data}" + x + "-" + config.data[x]);
                }
            }

            var req = $.ajax({
                url: uri ,
                headers: headers,
                method: method,
                data: data,
            });

            if(loaderView) {
                $( loaderView ).show();
            }else if(loaderModal){
                $(loaderModal).openModal();
            }else if(defLoader) {
                AnkieterApi.showLoader();
            }

            var onResult = function(){};
            if(config.onResult ){
                onResult = config.onResult;
            }
            var onError = function(){};
            if(config.onError ){
                onError = config.onError;
            }


            req.always(function (jqXHR, textStatus, errorThrown) {
                if(loaderView) {
                    $( loaderView ).hide();
                }else if(loaderModal){
                    $(loaderModal).closeModal();
                }else if(defLoader) {
                    AnkieterApi.hideLoader();
                }
            });


            req.done(function( msg, textStatus, jqXHR ){
                var statusCode =jqXHR.status;
                console.log("{"+statusCode+"}" + JSON.stringify( msg ));

                if(statusCode == 200) {
                    try {
                        var data = msg;//JSON.parse(msg);

                        if (data.success==1) {
                            if (onResult) {
                                onResult(data.data);
                            }
                        } else {
                            if (onError) {
                                onError(data.code);
                            }
                        }

                    } catch (ex) {
                        console.error("JSON PARSE EXCEPTION");
                        console.error(ex);
                        if (onError) {
                            onError("JSON PARSE EXCEPTION");
                        }
                    }
                }else{
                    if (onError) {
                        onError("ERROR HTTP" + textStatus);
                    }
                }
            });

            req.fail(function( jqXHR, textStatus, errorThrown ) {
                console.error("{"+textStatus+"}" );
                if (onError) {
                    onError("ERROR HTTP" + textStatus);
                }
            });

            this.cancel = function(){
                req.abort();
            };
        }

    }
};