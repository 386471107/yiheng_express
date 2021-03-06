
wx.ready(function () {
 

  // 5 图片接口
  // 5.1 拍照、本地选图
  var images = {
    localId: [],
    serverId: []
  };
  document.querySelector('#chooseImage').onclick = function () {
    wx.chooseImage({
	count:1,
      success: function (res) {
		
        images.localId = res.localIds;
       // alert('已选择 ' + res.localIds.length + ' 张图片');
		//alert(images.localId);
	
		//上传图片
			if (images.localId.length == 0) 
				 {
			  alert('请先选择图片');
			  return;
			}
			var i = 0, length = images.localId.length;
			images.serverId = [];
			function upload() {
			  wx.uploadImage({
				localId: images.localId[i],
				success: function (res) {
				  i++;
				  //alert('已上传：' + i + '/' + length);
				  images.serverId.push(res.serverId);
				 // alert(images.serverId);
				 
				 $.post("{php echo $this->createMobileUrl('home')}", { "op":"del"},
					function(data)
					{
					console.log(data.success)
					if (data.success==1)
						{
						  $("div").remove("#list"+value);
						}  
					}, "json");
				  
				  if (i < length) {
					upload();
				  }
				},
				fail: function (res) {
				  alert(JSON.stringify(res));
				}
			  });
			}
			upload();
				
	
		$("#chooseImages").append("<img src='"+images.localId+"' class='bmimg' />");
      }
    });
  };

  // 5.2 图片预览
  document.querySelector('#previewImage').onclick = function () {
    wx.previewImage({
      current: 'http://img5.douban.com/view/photo/photo/public/p1353993776.jpg',
      urls: [
        'http://img3.douban.com/view/photo/photo/public/p2152117150.jpg',
        'http://img5.douban.com/view/photo/photo/public/p1353993776.jpg',
        'http://img3.douban.com/view/photo/photo/public/p2152134700.jpg'
      ]
    });
  };

  // 5.3 上传图片
  document.querySelector('#uploadImage').onclick = function () {
    if (images.localId.length == 0) {
      alert('请先选择图片');
      return;
    }
    var i = 0, length = images.localId.length;
    images.serverId = [];
    function upload() {
      wx.uploadImage({
        localId: images.localId[i],
        success: function (res) {
          i++;
          //alert('已上传：' + i + '/' + length);
          images.serverId.push(res.serverId);
          if (i < length) {
            upload();
          }
        },
        fail: function (res) {
          alert(JSON.stringify(res));
        }
      });
    }
    upload();
  };

  // 5.4 下载图片
  document.querySelector('#downloadImage').onclick = function () {
    if (images.serverId.length === 0) {
      alert('请先使用 uploadImage 上传图片');
      return;
    }
    var i = 0, length = images.serverId.length;
    images.localId = [];
    function download() {
      wx.downloadImage({
        serverId: images.serverId[i],
        success: function (res) {
          i++;
          alert('已下载：' + i + '/' + length);
          images.localId.push(res.localId);
          if (i < length) {
            download();
          }
        }
      });
    }
    download();
  };


  
});

wx.error(function (res) {
  alert(res.errMsg);
});

