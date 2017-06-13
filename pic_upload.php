<?php
/**
 * 上传图片
 * @access public
 * @param  string         $path  上传图片存放的文件夹
 * @param  int|string     $iw    设置生成缩略图的宽,默认5000
 * @param  int|string     $ih    设置生成缩略图的高，默认5000
 * @return array          $img_path 存放图片路径的数组
 */
function picuploads($path,$iw='5000',$ih='5000')
{
    $upload = new \Think\Upload();// 实例化上传类
    $upload->maxSize         = 3145728 ;// 设置附件上传大小
    $upload->exts            = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
    $upload->rootPath   = './'; // 设置附件上传根目录
    $upload->savePath= 'Uploads/groupQrcode/'; //设置上传的目录(如果文件夹不存在，自动生成)
    $upload->saveName = 'Code_group'.
    // 上传图片文件
    $info   =   $upload->upload();
    //实例化图片处理类
    $image = new \Think\Image();

    /* --- 从这里开始做截图图片 start ---*/
    //多张图片生成缩略图
    foreach($info as $v){
        $infourl= './'.$v['savepath'].$v['savename'];
        //打开已经上传好的图片
        $image->open($infourl);
        $width  = $image->width(); // 返回图片的宽度
        $height = $image->height(); // 返回图片的高度
        //比较2个值的大小，取小值
        if($iw>$width){
            $iw = $width;
        }
        if($ih>$height){
            $ih = $height;
        }
        //把图片裁剪之后按原来的地址保存起来
        $image->thumb($iw, $ih,\Think\Image::IMAGE_THUMB_CENTER)->save($infourl);
        $img_path[]=$infourl;//把每次循环的处理过的图片的地址保存起来
    }
    return $img_path;
    /* --- 从这里结束做截图图片 end ---*/
}