<?php 

for($i=0;$i<20;$i++) { 

echo 'printing...<br />'; ob_flush(); flush(); usleep(300000); 
} 

?> 



<?php
public
function actionUpdate($id)
{
    $model = Signature::findOne(['user_id' => $id,]);
    if ($model->load(Yii::$app->request->post())) {
        $model->file = UploadedFile::getInstance($model, 'file');
        $model->file->saveAs('uploads/signature/' . $model->user_id . '.' . $model->file->extension);
        $model->url = 'uploads/signature/' . $model->user_id . '.' . $model->file->extension;
        if ($model->save()) {
            echo 1;
        } else {
            echo 0;
            echo $model->file;
        }
    } else {
        return $this->renderAjax('update', ['model' => $model,]);
    }
}


?>

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>
<?= $form->field($model, 'title') ?>
<?= $form->field($model, 'imageFile')->fileInput() ?>
<button type="button" class="btn btn-success subm">Upload</button>
<?php ActiveForm::end(); ?>

<script> $('.subm').click(function (e) {
        var formData = new FormData($('form')[0]);
        console.log(formData);
        $.ajax({url: "some_php_file.php", //Server script to process data type: 'POST', // Form data data: formData, beforeSend: beforeSendHandler, // its a function which you have to define success: function(response) { console.log(response); }, error: function(){ alert('ERROR at PHP side!!'); }, //Options to tell jQuery not to process data or worry about content-type. cache: false, contentType: false, processData: false }); }); </script>


 $( '#my-form' ) .submit( function( e ) { $.ajax( { url: 'http://host.com/action/', type: 'POST', data: new FormData( this ), processData: false, contentType: false } ); e.preventDefault(); } ); 
