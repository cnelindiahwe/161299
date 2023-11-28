<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="<?php echo site_url(); ?>web/zt2016/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <title>Document</title>

</head>
<body>
<div class="container" style="margin-top: 226px;margin-left: 339px;">
<?php

if($this->session->flashdata('ErrorMessage')){		
    $page_content.='<div class="alert alert-danger" role="alert" style="max-width:500px;">'."\n";
    $page_content.='  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'."\n";
    $page_content.='  <span class="sr-only">Error:</span>'."\n";
    $page_content.=$this->session->flashdata('ErrorMessage');
    $page_content.='</div>'."\n";
  }
  echo $page_content;

  ?>
    <div class="row">
        <div class="col-sm-12">
            <h2>Download File</h2>
            <form action="" method="POST">
                <div class="row">
                    <div class="col-sm-6">
                           <input type="password" style="display:none;"/>
                        <input type="password" class="form-control" autocomplete="off" name="G_security" placeholder="Enter Password..." />
                      
                    </div>
                    <div class="col-sm-6">
                        <button type="submit" class="btn btn-success">Download File</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>

