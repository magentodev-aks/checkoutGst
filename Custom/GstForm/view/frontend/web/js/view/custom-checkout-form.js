/*global define*/
define([
    'Magento_Ui/js/form/form',
    'jquery',
    'mage/url'
], function(Component, $, url) {
    'use strict';
    url.setBaseUrl(BASE_URL);

    return Component.extend({
        initialize: function () {
            this._super();
            // component initialization logic
            return this;
        },
        checksum: function(g){
                let regTest = /\d{2}[A-Z]{5}\d{4}[A-Z]{1}[A-Z\d]{1}[Z]{1}[A-Z\d]{1}/.test(g)
                 if(regTest){
                    let a=65,b=55,c=36;
                    return Array['from'](g).reduce((i,j,k,g)=>{ 
                       p=(p=(j.charCodeAt(0)<a?parseInt(j):j.charCodeAt(0)-b)*(k%2+1))>c?1+(p-c):p;
                       return k<14?i+p:j==((c=(c-(i%c)))<10?c:String.fromCharCode(c+b));
                    },0); 
                }
                return regTest
            },
        onSubmit: function() {
            // trigger form validation
            this.source.set('params.invalid', false);
            this.source.trigger('customCheckoutForm.data.validate');

            // verify that form data is valid
            if (!this.source.get('params.invalid')) {
                // data is retrieved from data provider by value of the customScope property
                var formData = this.source.get('customCheckoutForm');
                var controller = url.build('gst/index/gstdata');
                // do something with form data
                console.log(formData['gst_number']);


                $.ajax({
                    url: controller, // Change the URL to match your controller
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            // Handle success
                            alert(response.message);
                        } else {
                            // Handle error
                            alert(response.message);
                        }
                    }
                });
            }
        }
    });
});
