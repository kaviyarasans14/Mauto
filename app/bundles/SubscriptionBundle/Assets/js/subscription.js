Mautic.subscriptionOnLoad = function (container) {
    mQuery('#subscription-panel').on('show.bs.collapse', function (e) {
        var actives = mQuery('#subscription-panel').find('.in, .collapsing');
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
        mQuery('#selected-plan').html(planname);
        mQuery('#planamount-desc').attr("plancurrency", plancurrency);
        mQuery('#planamount-desc').attr("planamount", planamount);
        mQuery('#planamount-desc').attr("plankey", plankey);
        mQuery('#planamount-desc').attr("planname", planname);
        mQuery('#payment-monthly').trigger('click');
        mQuery('a[aria-controls="sectionTwo"]').trigger('click');
    });
    mQuery('[data-frequencybtn]').click(function(e) {
       // e.preventDefault();
        var currentLink = mQuery(this);
        var frequency = currentLink.attr('data-frequencybtn');
        var pldesc = mQuery('#planamount-desc');
        var planamount = pldesc.attr('planamount');
        var plancurrency = pldesc.attr('plancurrency');
       if(frequency == "year"){
           planamount=planamount*12;
           mQuery('#paymentduration-desc').html("Amount to be paid per year");
       }else{
           mQuery('#paymentduration-desc').html("Amount to be paid per month");
       }
        mQuery('#planamount-desc').attr("totalamount", planamount);
        mQuery('#planamount-desc').attr("plancycle", frequency);
        mQuery('#planamount-desc').html(plancurrency+planamount);
    });
    mQuery('#paymentcontinue-btn').click(function(e) {
        e.preventDefault();
        var pldesc = mQuery('#planamount-desc');
        var plankey = pldesc.attr('plankey');
        var planname = pldesc.attr('planname');
        var plancurrency = pldesc.attr('plancurrency');
        var actualamount = pldesc.attr('totalamount');
        var plancycle = pldesc.attr('plancycle');
        var taxamount=0;
        var totalamount=0;
        if(plancurrency == "₹"){
            taxamount= (actualamount * 18)/100;
            totalamount=(+actualamount + +taxamount);
        }else{
            totalamount=actualamount;
        }
        totalamount= Math.round(totalamount);
        taxamount= Math.round(taxamount);
        mQuery('#sectionThree .cplantitle').html(planname);
        mQuery('#sectionThree .cplantitle').attr("planname", plankey);
        mQuery('#sectionThree .cplantitle').attr("plancycle", plancycle);
        mQuery('#sectionThree .cplantitle').attr("plancurrency", plancurrency);
        mQuery('#sectionThree .cplanamount').html(actualamount);
        mQuery('#sectionThree .cplantax').html(taxamount);
        mQuery('#sectionThree .cplantotal').html(totalamount);
        mQuery('#sectionThree .cplanamtcurrency').html(plancurrency);
        mQuery('#sectionThree .cplantaxcurrency').html(plancurrency);
        mQuery('#sectionThree .cplantotalcurrency').html(plancurrency);
        mQuery('a[aria-controls="sectionThree"]').trigger('click');
    });
    mQuery('#paymentconfirm-btn').click(function(e) {
        e.preventDefault();
        mQuery('a[aria-controls="sectionFour"]').trigger('click');
    });
    mQuery('#makepayment-btn').click(function(e) {
        e.preventDefault();
        var usernameel=mQuery('#sectionFour #payment-username');
        var useremailel=mQuery('#sectionFour #payment-useremail');
        var useraddressel=mQuery('#sectionFour #payment-useraddress');
        var username=usernameel.val();
        var useremail=useremailel.val();
        var useraddress=useraddressel.val();
        var divwrapper = mQuery(usernameel).closest('div');
        if(username == ""){
            divwrapper.addClass('has-error');
            return;
        }else{
            divwrapper.removeClass('has-error');
        }
        divwrapper = mQuery(useremailel).closest('div');
        if(useremail == ""){
            divwrapper.addClass('has-error');
            return;
        }else{
            divwrapper.removeClass('has-error');
        }
        divwrapper = mQuery(useraddressel).closest('div');
        if(useraddress == ""){
            divwrapper.addClass('has-error');
            return;
        }else{
            divwrapper.removeClass('has-error');
        }
        var planname=mQuery('#sectionThree .cplantitle').attr("planname");
        var plancycle=mQuery('#sectionThree .cplantitle').attr("plancycle");
        var plancurrency=mQuery('#sectionThree .cplantitle').attr("plancurrency");
        Mautic.activateBackdrop();
        Mautic.ajaxActionRequest('subscription:makepayment', {username: username,useremail:useremail,useraddress:useraddress,planname:planname,plancycle:plancycle,plancurrency:plancurrency}, function(response) {
            Mautic.deactivateBackgroup();
          if (response.success) {
              if(response.provider == "razorpay"){
                  Mautic.invokeRazorPay(response,username,useremail,useraddress,planname);
              }else{
                  Mautic.invokePaypalPay(response);
              }
           }else{
              alert(response.errormsg);
          }
        });

    });
}

Mautic.invokeRazorPay = function(response,username,useremail,useraddress,planname) {
    var apikey=response.apikey;
    var subscriptionid=response.subscriptionid
    var options = {
        "key": apikey,
        "subscription_id": subscriptionid,
        "name": "LeadsEngage",
        "description": planname+" plan upgradtion",
        "image": "https://s3.amazonaws.com/leadsroll.com/home/leadsengage_logo-black.png",
        "handler": function (response){
            var paymentid=response.razorpay_payment_id;
            var subscriptionid=response.razorpay_subscription_id;
            var signature=response.razorpay_signature;
            Mautic.ajaxActionRequest('subscription:validatepayment', {paymentid: paymentid,subscriptionid:subscriptionid,signature:signature}, function(response) {
                if (response.success) {
                    Mautic.redirectWithBackdrop(response.redirect);
                }else{
                    alert(response.errormsg);
                }
            });
        },
        "prefill": {
            "name": username,
            "email":useremail,
            "contact":"",
            "method":"card"//{card|netbanking|wallet|emi|upi}
        },
        "notes": {
            "address": useraddress
        },
        "theme": {
            "color": "#0066cc"
        }
    };
    var rzp1 = new Razorpay(options);
    rzp1.open();
};
Mautic.invokePaypalPay = function(response) {
    Mautic.redirectWithBackdrop(response.approvalurl);
   // Mautic.openInNewTab(response.approvalurl);
};