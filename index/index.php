<?php

$post = !empty($_POST) ? $_POST : '';

$file = !empty($_FILES['file'])  ? $_FILES['file'] : '';

if(!$post || !$file){

    echo json_encode(['code' => 1001, 'msg' => 'error', 'data' => []]);exit;
}
$uploadSliceFile = new uploadSliceFile($post['filename'], $post['page'], $post['pages'], $file['tmp_name']);

$uploadSliceFile ->apiReturn();




class uploadSliceFile{

    private $filename;                              // 文件名

    private $filepath = "../file/uploads/";         // 分片上传目录

    private $filespath = '../file/upload/';         // 文件上传目录

    private $sliceNum;                              // 分块数（第几页

    private $tmpPath;                               // 上传文件临时目录

    private $totalBlobNum;                          // 总块数（总页数）

    public function __construct($filename, $sliceNum, $totalBlobNum, $tmpPath){

        $this ->filename = $filename;
        
        $this ->sliceNum = $sliceNum;

        $this ->totalBlobNum = $totalBlobNum;

        $this ->tmpPath = $tmpPath;

        $this ->filepath .= md5($this ->filename);

        $this ->moveFile();

        $this ->fileMerge();
    }





    //API返回数据
    public function apiReturn(){
        if($this->sliceNum == $this->totalBlobNum - 1){
 
            if(file_exists($this->filespath.'/'. $this->filename)){
 
                $data['code'] = 1;
 
                $data['msg'] = 'success';
 
                $data['file_path'] = $this->filespath.'/'. $this->filename;
 
            }
 
        }else{
 
            if(file_exists($this->filepath.'/'. $this->filename.'_'.$this->sliceNum)){
 
                $data['code'] = -1;
 
                $data['msg'] = 'waiting';
 
                $data['file_path'] = '';
 
            }
 
        }
 
        // header('Content-type: application/json');
 
        echo json_encode($data);
 
    }

    //移动文件
    private function moveFile(){
 
        $this->touchDir();
 
        $filename = $this->filepath.'/'. $this->filename.'_'.$this->sliceNum;
 
        move_uploaded_file($this->tmpPath,$filename);
 
    }

    //建立上传文件夹
    private function touchDir(){
 
        if(!file_exists($this->filepath)){
 
            return mkdir($this->filepath);
 
        }
 
    }

    //判断是否是最后一块，如果是则进行文件合成并且删除文件块
    private function fileMerge(){
 
        if($this->sliceNum == $this->totalBlobNum - 1){
 
            $blob = '';
 
            for($i = 0; $i < $this->totalBlobNum; $i++){
 
                $blob = file_get_contents($this->filepath.'/'. $this->filename.'_'.$i);
 
                file_put_contents($this->filespath.'/'. $this->filename,$blob,FILE_APPEND);
 
            }

            $this->deleteFileBlob();
        }
    }

    //删除文件块
    private function deleteFileBlob(){
 
        for($i = 0; $i < $this->totalBlobNum; $i++){
 
            unlink($this->filepath.'/'. $this->filename.'_'.$i);
            @rmdir($this->filepath);
 
        }
 
    }
}
