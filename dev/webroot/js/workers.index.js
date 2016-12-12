$(document).ready(function(){
    $('.generate_report').click(function(e){
        e.preventDefault();
        $(this).parent('form').find('input[name="SfWorkerBuildings[codArn]"]').val($('#sfworkerbuildings-codarn').val());
        $(this).parent('form').submit();
    });
});