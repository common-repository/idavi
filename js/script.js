jQuery(document).ready(function(){
    // email sub event
    jQuery('#ab_sub_button').live('click',function(){
        var email = jQuery('#sub_email').val();
        jQuery('#ab_sub_button').text('Please wait...');
        if(email == ''){
            alert('Please enter an email address');
            return;
        }
        // send sub action to admin 
        jQuery.post(ajaxurl,{action: 'ab_subscribe', email : email},function(data){ jQuery('#sub_box').after('<div style="margin-bottom: 5px; padding: 5px; font-size: 10px;">' + data + '</div>').hide()});
    });
    
       // jQuery.plot(jQuery("#placeholder"), [{label: "emails for " + month, data: d3,  bars: {show: true, align: "center"} }],{ grid: {hoverable: true},xaxis:{ ticks: t3}});

        function showTooltip(x, y, contents) {
            jQuery('<div id="tooltip">' + contents + '</div>').css( {
                position: 'absolute',
                display: 'none',
                top: y + 5,
                left: x + 5,
                border: '1px solid #fdd',
                padding: '2px',
                'background-color': '#fee',
                opacity: 0.80
            }).appendTo("body").fadeIn(200);
        }

        var previousPoint = null;
        jQuery("#placeholder").bind("plothover", function (event, pos, item) {
            jQuery("#x").text(pos.x.toFixed(2));
            jQuery("#y").text(pos.y.toFixed(2));
            if (item) {
                if (previousPoint != item.datapoint) {
                    previousPoint = item.datapoint;

                    jQuery("#tooltip").remove();
                    var x = item.datapoint[0].toFixed(2),
                    y = item.datapoint[1].toFixed(2);
                    showTooltip(item.pageX, item.pageY,
                    parseInt(y) + ' emails sent');
                }
            }
            else {
                jQuery("#tooltip").remove();
                previousPoint = null;            
            }
        });     
           
});