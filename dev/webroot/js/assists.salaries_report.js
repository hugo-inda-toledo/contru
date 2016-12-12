$(document).ready(function() {
    $(document).on('change', 'input[data-type="other_discounts"]', function(event) {
        event.preventDefault();
        var other_discounts = parseInt($(this).val());
        var total_discounts = parseInt($(this).parent().parent().siblings('td.total_discounts').children('input[data-type="total_discounts"]').attr('data-original'));
        var total_assets = parseInt($(this).parent().parent().siblings('td.total_assets').children('input[data-type="total_assets"]').val());
        total_discounts += other_discounts;
        var liquid_to_pay = total_assets - total_discounts;
        $(this).parent().parent().siblings('td.total_discounts').children('input[data-type="total_discounts"]').val(total_discounts);
        $(this).parent().parent().siblings('td.liquid_to_pay').children('input[data-type="liquid_to_pay"]').val(liquid_to_pay);
        $(this).parent().parent().siblings('td.total_discounts').children('span').text('$ ' + numberWithPoints(total_discounts));
        $(this).parent().parent().siblings('td.liquid_to_pay').children('span').text('$ ' + numberWithPoints(liquid_to_pay));
    });

    $(document).on('change', 'input[data-type="travel_expenses"]', function(event) {
        event.preventDefault();
        var travel_expenses = parseInt($(this).val());
        var total_taxable = parseInt($(this).parent().parent().siblings('td.total_taxable').children('input[data-type="total_taxable"]').attr('data-original'));
        var total_not_taxable = parseInt($(this).parent().parent().siblings('td.total_not_taxable').children('input[data-type="total_not_taxable"]').attr('data-original'));
        var total_assets = parseInt($(this).parent().parent().siblings('td.total_assets').children('input[data-type="total_assets"]').attr('data-original'));
        var total_discounts = parseInt($(this).parent().parent().siblings('td.total_discounts').children('input[data-type="total_discounts"]').val());
        total_not_taxable += travel_expenses;
        total_assets = total_taxable + total_not_taxable;
        var liquid_to_pay = total_assets - total_discounts;
        $(this).parent().parent().siblings('td.total_not_taxable').children('input[data-type="total_not_taxable"]').val(total_not_taxable);
        $(this).parent().parent().siblings('td.total_assets').children('input[data-type="total_assets"]').val(total_assets);
        $(this).parent().parent().siblings('td.liquid_to_pay').children('input[data-type="liquid_to_pay"]').val(liquid_to_pay);
        $(this).parent().parent().siblings('td.total_not_taxable').children('span').text('$ ' + numberWithPoints(total_not_taxable));
        $(this).parent().parent().siblings('td.total_assets').children('span').text('$ ' + numberWithPoints(total_assets));
        $(this).parent().parent().siblings('td.liquid_to_pay').children('span').text('$ ' + numberWithPoints(liquid_to_pay));
    });
    var tableWidth = $(".table-scroll").width();
    var contentTableWidth = $(".table-scroll > table").width();
    $(".table-scroll-1").css({
        'width': tableWidth,
    });
    $(".scroller").css({
        'width': contentTableWidth,
    });
    $(".table-scroll-1").scroll(function(){
        $(".table-scroll")
            .scrollLeft($(".table-scroll-1").scrollLeft());
    });
    $(".table-scroll").scroll(function(){
        $(".table-scroll-1")
            .scrollLeft($(".table-scroll").scrollLeft());
    });
});
