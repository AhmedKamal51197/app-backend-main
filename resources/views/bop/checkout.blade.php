<html>
<body>
<!-- Form with all request fields as prepared in PHP code above. Note all
fields are hidden -->
<form method="post" name="paymentForm" id="paymentForm"
      action="https://e-commerce-uat.bop.ps/EcomPayment/RedirectAuthLink">
    @foreach($params as $key=>$value)
        <input type="hidden" name="{{ $key }}" value="{{ $value }}"><br>
    @endforeach
    <input type="submit" value="Submit">
</form>
<!-- Automatic submission of request form to upon load using JavaScript -->
{{--<script language="JavaScript">--}}
{{--    document.forms["paymentForm"].submit();--}}
{{--</script>--}}
</body>
</html>
