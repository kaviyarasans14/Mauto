Mautic.prepaidplansOnLoad = function (container) {
    mQuery('#prepaidplan-panel').on('show.bs.collapse', function (e) {
        var actives = mQuery('#prepaidplan-panel').find('.in, .collapsing');
        actives.each(function (index, element) {
            mQuery(element).collapse('hide');
            var id = mQuery(element).attr('id');
            mQuery('a[aria-controls="' + id + '"]').addClass('collapsed');
            mQuery('#'+id).removeClass('show');
        })
    });
    mQuery('[data-plankey]').click(function(e) {
        e.preventDefault();
        var currentLink = mQuery(this);
        var plankey = currentLink.attr('data-plankey');
        var planname = currentLink.attr('data-planname');
        var planamount = currentLink.attr('data-planamount');
        var plancurrency = currentLink.attr('data-plancurrency');
        var plancredits = currentLink.attr('data-plancredits');
        Mautic.ajaxActionRequest('subscription:getAvailableCount', {}, function(response) {
            Mautic.deactivateBackgroup();
            if (response.success) {
               var availablecredits=response.availablecount;
               var totalcredits=(+plancredits + +availablecredits);
                mQuery('#sectionTwo #selected-plan').html(planname);
                mQuery('#sectionTwo #available-credits').html(Mautic.getFormattedNumber(availablecredits));
                mQuery('#sectionTwo #additional-credits').html(Mautic.getFormattedNumber(plancredits));
                mQuery('#sectionTwo #total-credits').html(Mautic.getFormattedNumber(totalcredits));
                var paymentcontinue = mQuery('#sectionTwo #paymentcontinue-btn');
                paymentcontinue.attr("plankey", plankey);
                paymentcontinue.attr("planname", planname);
                paymentcontinue.attr("planamount", planamount);
                paymentcontinue.attr("plancurrency", plancurrency);
                paymentcontinue.attr("plancredits", plancredits);
                paymentcontinue.attr("beforecredits", availablecredits);
                paymentcontinue.attr("aftercredits", totalcredits);
                mQuery('a[aria-controls="sectionTwo"]').trigger('click');
            }
        });

    });
    mQuery('#paymentcontinue-btn').click(function(e) {
        e.preventDefault();
        var paymentcontinue = mQuery('#sectionTwo #paymentcontinue-btn');
        var plankey=paymentcontinue.attr("plankey");
        var planname=paymentcontinue.attr("planname");
        var planamount=paymentcontinue.attr("planamount");
        var plancurrency=paymentcontinue.attr("plancurrency");
        var plancredits=paymentcontinue.attr("plancredits");
        var beforecredits=paymentcontinue.attr("beforecredits");
        var aftercredits=paymentcontinue.attr("aftercredits");
        var planpricing = mQuery('#sectionThree #plan-pricing');
        var taxheaderlabel = mQuery('#sectionThree #tax-label');
        var taxamountlabel = mQuery('#sectionThree #tax-amount');
        var totalamountlabel = mQuery('#sectionThree #total-amount');
        var pricingcurrency = mQuery('#sectionThree .pricing-currency');
        var taxcurrency = mQuery('#sectionThree .tax-currency');
        var totalcurrency = mQuery('#sectionThree .total-currency');

        var taxamount=0;
        var totalamount=0;
        if(plancurrency == "₹"){
            taxamount= (planamount * 18)/100;
            taxamount= Math.round(taxamount);
            totalamount=(+planamount + +taxamount);
            taxheaderlabel.html("Tax (18%)");
            taxamountlabel.html(Mautic.getFormattedNumber(taxamount));
        }else{
            taxcurrency.hide();
            totalamount=planamount;
            taxamountlabel.html("NA");
            taxheaderlabel.html("Tax");
        }
        planpricing.html(Mautic.getFormattedNumber(planamount));
        totalamountlabel.html(Mautic.getFormattedNumber(totalamount));
        taxcurrency.html(plancurrency);
        pricingcurrency.html(plancurrency);
        totalcurrency.html(plancurrency);
        var makepayment = mQuery('#sectionThree #makepayment-btn');
        makepayment.attr("plankey",plankey);
        makepayment.attr("planname",planname);
        makepayment.attr("plancurrency",plancurrency);
        makepayment.attr("plancredits",plancredits);
        makepayment.attr("aftercredits",aftercredits);
        makepayment.attr("beforecredits",beforecredits);
        makepayment.attr("totalamt",totalamount);
        mQuery('a[aria-controls="sectionThree"]').trigger('click');
    });
    mQuery('#makepayment-btn').click(function(e) {
        e.preventDefault();
        var makepayment = mQuery('#sectionThree #makepayment-btn');
        var plankey=makepayment.attr("plankey");
        var planname=makepayment.attr("planname");
        var plancurrency=makepayment.attr("plancurrency");
        var plancredits=makepayment.attr("plancredits");
        var beforecredits=makepayment.attr("beforecredits");
        var aftercredits=makepayment.attr("aftercredits");
        var totalamount=makepayment.attr("totalamt");

        Mautic.activateBackdrop();
        Mautic.ajaxActionRequest('subscription:purchaseplan', {plancurrency:plancurrency,planamount:totalamount,planname:planname,plankey:plankey,plancredits:plancredits,beforecredits:beforecredits,aftercredits:aftercredits}, function(response) {
            Mautic.deactivateBackgroup();
          if (response.success) {
              if(response.provider == "razorpay"){
                  Mautic.invokeRazorPay_Prepaid(response,plankey,planname,totalamount);
              }else{
                  Mautic.invokePaypalPay_Prepaid(response);
              }
           }else{
              alert(response.errormsg);
          }
        });

    });
}

Mautic.invokeRazorPay_Prepaid = function(response,plankey,planname,totalamount) {
    var apikey=response.apikey;
    var username=response.username;
    var useremail=response.useremail;
    var usermobile=response.usermobile;
    var captureamount=(totalamount * 100); // convert to paise
    var options = {
        "key": apikey,
        "amount": captureamount,
        "name": planname,
        "description": "Order ID:"+response.orderid,
        "image": "https://s3.amazonaws.com/leadsroll.com/Razer-Pay-Icon.png",
        "handler": function (response){
            Mautic.activateBackdrop();
            var paymentid=response.razorpay_payment_id;
            Mautic.ajaxActionRequest('subscription:capturepayment', {paymentid: paymentid,captureamount:captureamount}, function(response) {
                if (response.success) {
                    Mautic.redirectWithBackdrop(response.redirect);
                }else{
                    Mautic.deactivateBackgroup();
                    alert(response.errormsg);
                }
            });
        },
        "prefill": {
            "name": username,
            "email":useremail,
            "contact":usermobile,
            // "method":"card"//{card|netbanking|wallet|emi|upi}
        },
        "notes": {
            "merchant_order_id": response.orderid,
            "plankey": plankey
        },
        "theme": {
            "color": "#0066cc"
        },
        "modal": {
            "ondismiss":  function (response){
              //  alert("onDismiss Calling....");
            }
        }
    };
    var rzp1 = new Razorpay(options);
    rzp1.open();
};
Mautic.invokePaypalPay_Prepaid = function(response) {
    Mautic.redirectWithBackdrop(response.approvalurl);
   // Mautic.openInNewTab(response.approvalurl);
};

Mautic.getFormattedNumber = function(number) {
  return number.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,")
};
Mautic.loadLicenseUsageInfo = function() {
    Mautic.ajaxActionRequest('subscription:validityinfo', {}, function(response) {
        if (response.success) {
            if(response.credits != "" && response.validity != ""){
                mQuery('.sidebar-credits-info-holder').show();
                mQuery('.sidebar-credits-info-holder .email-credits').html("Available Credits : "+response.credits);
                mQuery('.sidebar-credits-info-holder .email-validity').html("Expiry Date : "+response.validity);
                mQuery('.sidebar-credits-info-holder .email-days-available').html("Days Available : "+response.daysavailable);
            }else{
                mQuery('.sidebar-credits-info-holder').hide();
            }

        }
    });
};