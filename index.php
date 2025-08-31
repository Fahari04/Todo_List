
<html>
<head>
  <!-- Latest compiled and minified CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
  <script src="//code.jquery.com/jquery.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
  <link rel="stylesheet" type="text/css" href="alertify/css/alertify.core.css">
  <link rel="stylesheet" type="text/css" href="alertify/css/alertify.default.css">
  <link rel="stylesheet" type="text/css" href="css/custom.css">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
  <title>TODO List</title>
</head>
<body>
  <div class="container">
    <div class="row">
      <div class="col-md-8 col-md-offset-2">
        <div class="main-card">
          <div class="alert alert-success" role="alert" style="overflow:hidden;">
            <h4>
              <span class="glyphicon glyphicon-list-alt"></span>
              TODO List Management <small style="color:#fff;font-size:60%;">using AWS EC2 &amp; LAMP by Fahari</small>
            </h4>
            <span class="realtime-clock">
              <span id="realtime-clock"></span>
            </span>
          </div>
          <div id="list">
            <?php include 'list.php' ?>
          </div>
          <div>
            <div class="input-group">
              <input type="text" class="form-control" id="txtNewItem" placeholder="Description of New Item">
              <span class="input-group-btn">
                <button class="btn btn-primary" id="addButton" onclick="return validateForm();" type="button">
                  <span class="glyphicon glyphicon-plus"></span> Add New
                </button>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="footer">
      &copy; <?php echo date('Y'); ?> TODO List by Fahari &middot; AWS EC2 &amp; LAMP Stack
    </div>
  </div>
  <script src="alertify/js/alertify.min.js"></script>
  <script>
    // Real-time clock
    function updateClock() {
      var now = new Date();
      var formatted = now.getFullYear() + "-" +
        ("0" + (now.getMonth() + 1)).slice(-2) + "-" +
        ("0" + now.getDate()).slice(-2) + " " +
        ("0" + now.getHours()).slice(-2) + ":" +
        ("0" + now.getMinutes()).slice(-2) + ":" +
        ("0" + now.getSeconds()).slice(-2);
      document.getElementById('realtime-clock').textContent = formatted;
    }
    setInterval(updateClock, 1000);
    updateClock();

    function validateForm(){
      var val=document.getElementById("txtNewItem").value;
      if (val.length<1) {
        alertify.error("Item description must contain a character");
        return false;
      }else{
        InsertItemInDatabase();
      }
    }
    function validateEdit(desc){
      var desc=document.getElementById("txtNewItem").value;
      if (desc.length<1) {
        alertify.error("Item description must contain at least a character");
        return false;
      }else{
        return true;
      }
    }
    function InsertItemInDatabase() {
      var buttonString= "<span class='glyphicon glyphicon-refresh glyphicon-refresh-animate' id='spinner'></span> "+$('#addButton').html();
      $('#addButton').html(buttonString);
      var new_desc=document.getElementById("txtNewItem").value;
      document.getElementById("txtNewItem").value="";
      $.ajax({
        url:'process.php?insert_description=' + encodeURIComponent(new_desc),
        complete: function (response) {
          var status = JSON.parse(response.responseText);
          if(status.status =="success"){
            alertify.success("New item has been added successfully");
          }else if(status.status =="error"){
            alertify.error("Error while adding the item");
          }
          $( "#list" ).load( "list.php");
          $( "#spinner" ).remove();
        },
        error: function () {}
      });
    }
    function DeleteItem(id) {
      alertify.confirm("Are you sure to delete this item?", function (e) {
        if (e) {
          var buttonString= "<span class='glyphicon glyphicon-refresh glyphicon-refresh-animate' id='spinner'></span> "+$('#delete_'+id).html();
          $('#delete_'+id).html(buttonString);
          $.ajax({
            url:'process.php?delete_id=' + id,
            complete: function (response) {
              var status = JSON.parse(response.responseText);
              if(status.delete_status =="success"){
                alertify.success("Item Deleted");
                $( "#list" ).load( "list.php" );
              }else if(status.delete_status =="error"){
                alertify.error("Error while deleting the item");
              }
            },
            error: function () {
              $('#output').html('Bummer: there was an error!');
            }
          });
        }
      });
    }
    function EditItem(id) {
      $.ajax({
        url:'process.php?edit_id=' + id,
        complete: function (response) {
          var status = JSON.parse(response.responseText);
          if(status.edit_status =="success"){
            alertify.success("Item Deleted");
            $( "#list" ).load( "list.php" );
          }else if(status.edit_status =="error"){
            alertify.error("Error while deleting the item");
          }
        },
        error: function () {
          $('#output').html('Bummer: there was an error!');
        }
      });
    }
    function checks(id,desc){
      alertify.prompt("Edit List Item, ID="+id, function (e, str) {
        if (e) {
          if (str.length>1) {
            var buttonString= "<span class='glyphicon glyphicon-refresh glyphicon-refresh-animate' id='spinner'></span> "+$('#edit_'+id).html();
            $('#edit_'+id).html(buttonString);
            $.ajax({
              url:'process.php',
              data : {edit_id:id, new_desc:str},
              complete: function (response) {
                var status = JSON.parse(response.responseText);
                if(status.edit_status =="success"){
                  alertify.success("Information updated successfully");
                  $( "#list" ).load( "list.php" );
                  $( "#spinner" ).remove();
                }else if(status.edit_status =="error"){
                  alertify.error("Error while editing the item");
                }
              },
              error: function () {
                $('#output').html('Bummer: there was an error!');
              }
            });
          }else{
            alertify.error("Item description must contain at least a characters. No changes made");
          }
        }
      }, desc);
    }
  </script>
</body>
</html>