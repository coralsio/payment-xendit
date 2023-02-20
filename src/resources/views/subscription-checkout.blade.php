@if($gateway->supportCards())
    <div class="row">
        <div class="col-md-12">
            @component('components.box')
                @slot('box_title')
                    @lang('Xendit::labels.checkout.title')
                @endslot
                <p></p>
                @php \Actions::do_action('pre_xendit_checkout_form',$gateway) @endphp

                <div class="row">
                    <!-- custom fields can be added here -->
                    <div class="col-md-6">
                        {!! CoralsForm::text('card_number','Xendit::attributes.card_number',true,'') !!}
                    </div>
                    <div class="col-md-3">
                        {!! CoralsForm::number('zip_code','Xendit::attributes.zip_code',true,'',[ 'placeholder'=>trans('Xendit::attributes.zip_code')]) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2 p-r-0">
                        {!! CoralsForm::number('expMonth','Xendit::attributes.expMonth',true,'',[ 'placeholder'=> "MM"]) !!}
                    </div>
                    <div class="col-md-2">
                        {!! CoralsForm::number('expYear','Xendit::attributes.expYear',true,'',['placeholder'=>"YY"]) !!}
                    </div>
                    <div class="col-md-2">
                        {!! CoralsForm::number('ccv','CCV',true,'',['placeholder'=>"CCV"]) !!}
                    </div>

                    <div class="col-md-3">
                        {!! CoralsForm::textarea('notes','Xendit::attributes.notes') !!}
                    </div>
                </div>
                <div id="payment-error" class="alert alert-danger" style="display: none"></div>

            @endcomponent
        </div>
    </div>
@else
    <div class="row">
        <div class="col-md-12">
            <h3>@lang('Xendit::labels.checkout.redirect_payment_link')</h3>
        </div>
    </div>
@endif


<script type="text/javascript">
    var isAjax = '{{ request()->ajax() }}';


    window.onload = () => {
        initXendit();
    }


    if (isAjax == '1') {
        initXendit();
    }

    function initXendit() {
        @if($gateway->supportCards())

        $.getScript(`https://js.xendit.co/v1/xendit.min.js`, function () {

            Xendit.setPublishableKey('{{$gateway->getPublicKey()}}');

            var $form = $('#payment-form');
            $form.on("submit", function (event) {
                event.preventDefault();

                if (!validateCardDetails()) {
                    stopLadda();
                    return false;
                }
                createToken();
                return false;
            });
        });
        @else
        $('#payment-form').on("submit", function (event) {
            event.preventDefault();
            submitForm();
            return false;
        });
        @endif
    }

    function validateCardDetails() {
        $('#payment-error').empty().slideUp();

        let isCardNumberValid = Xendit.card.validateCardNumber($('#card_number').val()),
            message = [];

        if (!isCardNumberValid) {
            message.push('The Card number is invalid!');
        }

        let isExpirationValid = Xendit.card.validateExpiry($('#expMonth').val(), $('#expYear').val());

        if (!isExpirationValid) {
            message.push('The Expiration date is invalid!');
        }


        let isCCVValid = Xendit.card.validateCvn($('#ccv').val());

        if (!isCCVValid || !$('#ccv').val()) {
            message.push('CCV in valid!');
        }


        if (message.length) {
            message.unshift('Request Payment Method Error:');
            $('#payment-error').html(message.join('<br/>')).slideDown();
            return false;
        }

        return true;

    }

    function createToken() {
        Xendit.card.createToken({
            card_number: $('#card_number').val(),
            card_exp_month: $('#expMonth').val(),
            card_exp_year: $('#expYear').val(),
            card_cvn: $('#ccv').val(),
            is_multiple_use: true,
            should_authenticate: true
        }, xenditResponseHandler);

        return false;
    }

    function xenditResponseHandler(err, creditCardToken) {

        if (err) {
            return handleErrors(err);
        }

        Xendit.card.createAuthentication({
            token_id: creditCardToken.id,
            amount: '{{Currency::getAmountISOCurrency(\Currency::convert($plan->price, 'USD', 'IDR', false),'IDR')}}'
        }, function (err, card) {

            if (err) {
                return handleErrors(err);
            }

            $form = $('#payment-form');
            // insert the token into the form so it gets submitted to the server
            $form.append(`<input type='hidden' name='checkoutToken' value='${card.credit_card_token_id}'/>`);

            submitForm();

        });


    }

    function submitForm() {
        $form = $('#payment-form');
        $form.addClass('ajax-form');
        $form.append("<input type='hidden' name='gateway' value='Xendit'/>");

        $form.get(0).submit();


    }


    function handleErrors(err) {
        let msgs = [];

        msgs.push(err.message);

        if (err.errors) {
            for (let e of err.errors) {
                msgs.push(e.message)
            }
        }

        $('#payment-error').html(msgs.join('<br/>')).slideDown();

        stopLadda();

        return false;
    }

    /**
     *
     */
    function stopLadda() {
        if (window.Ladda) {
            Ladda.stopAll();
        }
    }

</script>
