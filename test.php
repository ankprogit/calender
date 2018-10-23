function add_events(){
global $wpdb;
$userid=$_REQUEST['userid'];
$quantity=$_REQUEST['quantity'];
$date=$_REQUEST['start'];
$sdate=strtotime($date);

$date=date("Y-m-d",$sdate);


$k=$wpdb->insert( 
	'wp_bookings', 
	array( 
		'userid' =>$userid , 
		'itemname' => 'test',
		'date'=>$date,
		'quantity'=>$quantity

	), 
	array( 
		'%d',
		'%s',
		'%s', 
		'%d' 
	) 
);
If($k==1)
{
echo "success";
}else{
echo "Somthing went wrong";

}

wp_die();

//return print_r($m);



}

add_action( 'wp_ajax_add_events', 'add_events' );
add_action( 'wp_ajax_nopriv_add_events', 'add_events' );


<?php
/**
Template Name: addorder
 * The template for displaying home page.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package VW Tour Lite
 */

get_header(); 
$userid=get_current_user_id();
global $wpdb;
$sql = "SELECT * FROM wp_bookings WHERE userid=$userid";
$result = $wpdb->get_results($sql) or die(mysql_error());
$Tj=array();
    foreach( $result as $results ) {
$Tj1=array();
        $Tj1['title']= $results->itemname;
   $Tj1['start']= $results->date;
   $Tj[]=$Tj1;
    }
    echo "<pre>";
$fetchevnts=json_encode($Tj);
echo "</pre>";
?>
<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8' />
<link href='<?php echo get_template_directory_uri();?>/fullcalendar/fullcalendar.min.css' rel='stylesheet' />
<link href='<?php echo get_template_directory_uri();?>/fullcalendar/fullcalendar.print.min.css' rel='stylesheet' media='print' />
<script src='<?php echo get_template_directory_uri();?>/fullcalendar/lib/moment.min.js'></script>
<script src='<?php echo get_template_directory_uri();?>/fullcalendar/lib/jquery.min.js'></script>
<script src='<?php echo get_template_directory_uri();?>/fullcalendar/fullcalendar.min.js'></script>
<script>

  $(document).ready(function() {

    $('#calendar').fullCalendar({
      header: {
        left: 'next today',
        center: 'title',
        right: 'month,agendaWeek,agendaDay'
      },
      defaultDate: "<?php echo date("Y-m-d");?>",
      navLinks: true, // can click day/week names to navigate views
      selectable: true,
      selectHelper: true,
      select: function(start, end) {
        //alert(start+"ffffff"+end);
        //var title = prompt('Event Title1:');
      //  var text="";
       // var eventData;
      //  if (title) {
        //  eventData = {
       //     title: title,
        //    title: title,
       //     text:text,
         //   start: start,
         //   end: end
        //  };
        var start=start;
            $(".popup").css("display","block");
             $("#startdate").val(start);
            

              $(".submitForm").click(function(){
                  var title = $("#Quantity").val();
                 var userid= $("#userId").val();
                  var start= $("#startdate").val();

                   var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";

                   var check = $.fullCalendar.formatDate(start,'yyyy-MM-dd');
    var today = $.fullCalendar.formatDate(new Date(),'yyyy-MM-dd');
    if(check < today)
    {
        // Previous Day. show message if you want otherwise do nothing.
        // So it will be unselectable


                  if (title) {
                    
                    /*  $.ajax({
                          url: 'http://192.168.0.16/vik/fullcalendar/fullcalendar-3.9.0/demos/php/addevents.php',
                          data: 'title=' + title + '&start=' + start + '&end=' + end +  '&creator='+userid,
                          type: "POST",
                          success: function (json) {
                              console.log(json);
                              $('#calendar').fullCalendar('refetchEvents');
                          }
                      });*/

 
 $.ajax({
         type : "post",
         dataType : "json",
         url : ajaxurl,
         data : {action: "add_events", userid:userid, quantity:title,start:start},
         success: function(response) {
          console.log(response);
            if(response.type == "success") {
              // jQuery("#vote_counter").html(response.vote_count)
                 $('#calendar').fullCalendar('refetchEvents');
            }
            else {
               alert("Your vote could not be added")
            }
         }
      }) ;

    // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
   // $.post(ajaxurl, data, function(response) {
    //  alert('Got this from the server: ' + response);
    //});


                   console.log("start " + start + " end " + end);
                  }
                      }
    else
    {
       alert("You can not select previous dates");
    }


                $('#calendar').fullCalendar('unselect');
              });


          $('#calendar').fullCalendar('renderEvent', eventData, true); // stick? = true
        }
  
   ,
      editable: true,
      eventLimit: true, // allow "more" link when too many events
      events: <?php echo $fetchevnts; ?>
    });

  });

</script>
<style>

  body {
    margin: 40px 10px;
    padding: 0;
    font-family: "Lucida Grande",Helvetica,Arial,Verdana,sans-serif;
    font-size: 14px;
  }

  #calendar {
    max-width: 900px;
    margin: 0 auto;
  }

</style>
</head>
<body>
<div class="popup" style="display:none;
   position: absolute;
    z-index: 9999;
    background: grey;
    padding: 48px;
    text-align: center;
    margin: 3%;
">
  Milk Quantity<input type="number" name="quantity" id="Quantity" value="" /></br>
  USer<input type="hidden" name="userId" id="userId" value="<?php echo $userid;?>" />
   date<input type="text" name="start" id="startdate" value="" />

  <input type="submit" name="submit" id="submit" class="submitForm" value="Submit" />
</div>

  <div id='calendar'></div>

</body>
</html>

 <?php get_footer(); ?>
