Mautic.accountinfoOnLoad = function (container) {
    if(mQuery('.cardholder-panel').is(':visible')) {
        var stripe = getStripeClient();
        var card=getStripeCard(stripe);
        mountStripeCard(stripe,card,'#card-holder-widget');
        mQuery('.cardholder-panel .card-update-btn').click(function(e) {
            e.preventDefault();
            Mautic.activateBackdrop();
            stripe.createToken(card).then(function(result) {
                if (result.error) {
                    Mautic.deactivateBackgroup();
                    // Inform the user if there was an error.
                    var errorElement = document.getElementById('card-holder-errors');
                    errorElement.textContent = result.error.message;
                } else {
                    // Send the token to your server.
                    stripeTokenHandler(card,result.token,".cardholder-panel",null);
                }
            });
        });
    }
    if(mQuery('.cancelsubscription').is(':visible')) {
        mQuery('.cancelsubscription .cancel-subscription').click(function(e) {
            e.preventDefault();
            Mautic.activateBackdrop();
            Mautic.ajaxActionRequest('subscription:cancelsubscription', {}, function(response) {
                e.preventDefault();
                Mautic.activateBackdrop();
                Mautic.deactivateBackgroup();
                if(response.success) {
                    mQuery('.cancelsubscription').addClass('hide');
                    mQuery('.deactivatedaccount').addClass('show');
                }
            });
        });
    }
}
Mautic.pricingplansOnLoad = function (container) {
    var stripe = getStripeClient();
    var card=getStripeCard(stripe);
    mQuery('[data-planname]').click(function(e) {
        var currentLink = mQuery(this);
        var planname = currentLink.attr('data-planname');
        var planamount = currentLink.attr('data-planamount');
        var plancurrency = currentLink.attr('data-plancurrency');
        var plancredits = currentLink.attr('data-plancredits');
        var paynowbtn=mQuery('.pay-now-btn');
        var emailtransport=mQuery('.pricing-plan-holder').attr('data-email-transaport');
        if(emailtransport == 'viale' && planname == 'viaaws'){
    mQuery('#pricing-plan-alert-info').removeClass('hide');
}else{
            mQuery('#pricing-plan-alert-info').addClass('hide');
    paynowbtn.html("Pay Now"+" ("+plancurrency+planamount+")");
    paynowbtn.attr("planamount",planamount);
    paynowbtn.attr("plancurrency",plancurrency);
    paynowbtn.attr("plancredits",plancredits);
    paynowbtn.attr("planname",planname);
    mQuery('.pricing-type-modal-backdrop').removeClass('hide');
    mQuery('.pricing-type-modal').removeClass('hide');
    mountStripeCard(stripe,card,'#card-holder-widget');
}

    });
    mQuery('.pay-now-btn').click(function(e) {
        e.preventDefault();
        var currentLink = mQuery(this);
        var planamount = currentLink.attr('planamount');
        var plancurrency = currentLink.attr('plancurrency');
        var planname = currentLink.attr('planname');
        var plancredits = currentLink.attr('plancredits');
        Mautic.activateButtonLoadingIndicator(currentLink);
        stripe.createToken(card).then(function(result) {
            if (result.error) {
                Mautic.removeButtonLoadingIndicator(currentLink);
                // Inform the user if there was an error.
                var errorElement = document.getElementById('card-holder-errors');
                errorElement.textContent = result.error.message;
            } else {
                // Send the token to your server.
                stripeTokenHandler(card,result.token,'.pricing-type-modal',currentLink);
            }
        });
    });
}
function getStripeClient(){
    // Create a Stripe client.
    var stripe = Stripe('pk_live_SaCvf4xx8HojET3eQfTBhiY2');//pk_test_6ZK3IyRbtk82kqU1puGcg9i6
    return stripe;
}
function getStripeCard(stripe){
    // Create an instance of Elements.
    var elements = stripe.elements();
// Custom styling can be passed to options when creating an Element.
// (Note that this demo uses a wider set of styles than the guide below.)
    var style = {
        base: {
            color: '#32325d',
            lineHeight: '18px',
            fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
            fontSmoothing: 'antialiased',
            fontSize: '16px',
            '::placeholder': {
                color: '#aab7c4'
            }
        },
        invalid: {
            color: '#fa755a',
            iconColor: '#fa755a'
        }
    };
// Create an instance of the card Element.
    var card = elements.create('card', {style: style});
    return card;
}
function mountStripeCard(stripe,card,elementid){
// Add an instance of the card Element into the `card-element` <div>.
    card.mount(elementid);
// Handle real-time validation errors from the card Element.
    card.addEventListener('change', function(event) {
        var displayError = document.getElementById('card-holder-errors');
        if (event.error) {
            displayError.textContent = event.error.message;
        } else {
            displayError.textContent = '';
        }
    });
}

function stripeTokenHandler(card,token,rootclass,btnelement){
    clearInfoText();
    var chwidget = mQuery(rootclass+' #card-holder-widget');
    var letoken=chwidget.attr("data-le-token");
    var stripetoken=token.id;
    var planamount = 1;
    var plancurrency="$";
    var plancredits=0;
    var planname="";
    var isCardUpdateAlone=true;
    if(btnelement != null){
        planamount = btnelement.attr('planamount');
        plancurrency = btnelement.attr('plancurrency');
        planname = btnelement.attr('planname');
        plancredits = btnelement.attr('plancredits');
        isCardUpdateAlone=false;
    }
    // Insert the token ID into the form so it gets submitted to the server
    Mautic.ajaxActionRequest('subscription:updatestripecard', {letoken:letoken,stripetoken:stripetoken,planamount:planamount,plancurrency:plancurrency,plancredits:plancredits,planname:planname,isCardUpdateAlone:isCardUpdateAlone}, function(response) {

        if(isCardUpdateAlone){
            Mautic.deactivateBackgroup();
        }else{
            Mautic.removeButtonLoadingIndicator(mQuery('.pay-now-btn'));
        }
        if (response.success) {
            card.clear();
            if(isCardUpdateAlone){
                setInfoText("Card updated successfully");
                location.reload();
            }else{
                Mautic.redirectWithBackdrop(response.statusurl);
            }
        }
        else{
            // Inform the user if there was an error.
            setInfoText(response.errormsg);
        }
    });
}
function clearInfoText() {
    var infoElement = mQuery("#card-holder-info");
    infoElement.html("");
    infoElement.addClass('hide');
}
function setInfoText(info){
    var infoElement = mQuery("#card-holder-info");
    infoElement.html(info);
    infoElement.removeClass('hide');
}