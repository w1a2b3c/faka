var app = getApp(), utils = require("../../utils/util");
let interstitialAd = null
var e = null;

Page({

    data: {
        data: [],
        classArray: [],
        classIndex: 0,
        shareShow: !1,
        cid: ""
    },

    onLoad: function onLoad() {
this. interstitialAd();
      try {
        const res = wx.getSystemInfoSync()
        console.log(res.platform)
        if(res.platform=='windows'||res.platform=='mac'){
            wx.showModal({
             title: '温馨提示!',
             content:'请使用手机端登录',
             showCancel:false,
             success(res) {
              wx.exitMiniProgram()//关闭当前小程序
             }
            });
            return;
           }
      } catch (e) {
        // Do something when catch error
      }


       this.getClassDatas();
      
      },
      interstitialAd:function(){ 
      if (wx.createInterstitialAd) {
        interstitialAd = wx.createInterstitialAd({
          adUnitId: "adunit-3ce72f5fc97170c8"
        })
        interstitialAd.onLoad(() => {console.log('插屏 广告加载成功')})
        interstitialAd.onError((err) => {
          console.error('插屏广告加载失败', err)
        })
        interstitialAd.onClose(() => {console.log('插屏 广告关闭')})
      }
      if (interstitialAd) {
        interstitialAd.show().catch((err) => {
          console.error(err)
        })
      }
    },

    getClassDatas: function getClassDatas() {
        var that = this;
        wx.login({
            success: function success(code) {
                utils.getRequest("/Api/classDatas.php?code=" + code.code).then(function(res) {
                    console.log(res);
                    if (res.data.code == 200) {
                        that.setData({
                            data: res.data.data,
                            classArray: res.data.class,
                            cid: res.data.class[0].cid
                        });
                        wx.setNavigationBarTitle({
                            title: res.data.data.xcx_name
                        });
                    } else {
                        wx.showModal({
                            title: "提示",
                            content: res.data.msg,
                            showCancel: !1,
                            confirmText: "重试",
                            success: function success() {
                                that.getClassDatas();
                            }
                        });
                    }
                }).catch(function(res) {
                    console.log(res);
                    wx.showModal({
                        title: "提示",
                        content: "网络请求超时",
                        confirmText: "重试",
                        success: function success() {
                            that.getClassDatas();
                        }
                    });
                });
            }
        });
    },
    
    classChange: function classChange(e) {
        var cid = this.data.classArray[e.detail.value].cid;
        console.log("选的是", e.detail.value);
        console.log("选的名称", this.data.classArray[e.detail.value].name);
        console.log("选的名称ID", cid);
        this.setData({
            classIndex: e.detail.value,
            cid: cid
        });
    },
    receive: function receive() {
        var that = this;
        switch (that.data.data.examine) {
          case 0:
            that.ok();
            break;

          case 1:
            that.submitPay();
            break;

          case 2:
            this.initVideoAd(function() {
                that.ok();
            });
            break;

          case 3:
            this.initVideoAd(function() {
                that.setData({
                    shareShow: !0
                });
            });
            break;

          default:
            that.ok();
        }
    },
    popupShow: function popupShow() {
        this.setData({
            shareShow: !1
        });
    },
    ok: function ok(trade_no) {
      var that = this;
      wx.showLoading({
          title: "获取卡密中"
      });
      wx.login({
          success: function success(code) {
              utils.getRequest("/Api/getCarmel.php?code=" + code.code + "&cid=" + that.data.cid + "&trade_no=" + trade_no).then(function(res) {
                  console.log(res);  // 打印返回的数据，确认卡密字段是否正确
                  wx.hideLoading();
                  if (res.data.code == 200) {
                      let carmel = res.data.carmel;
  
                      // 处理卡密，确保去除任何无效字符
                      if (carmel) {
                          // 去除卡密中的空格和多余字符
                          carmel = carmel.trim();
  
                          // 如果卡密是 "null" 或者为空字符串，则替换为空
                          if (carmel === "null" || carmel === "") {
                              carmel = ''; // 如果卡密是无效的，设置为空字符串
                          }
                      }
  
                      // 如果卡密为空（即不存在有效卡密），则提示用户
                      if (!carmel) {
                          wx.showModal({
                              title: "领取失败",
                              content: "卡密无效，请稍后再试。",
                              showCancel: !1,
                              confirmText: "我知道了",
                              success: function success() {
                                  that.setData({
                                      shareShow: !1
                                  });
                              }
                          });
                          return; // 如果没有有效卡密，直接退出
                      }
  
                      // 显示领取成功的弹框
                      wx.showModal({
                          title: "领取成功",
                          content: "卡密: " + carmel,
                          confirmText: "复制卡密",
                          showCancel: !1,
                          success: function success(o) {
                              // 处理拼接内容，确保如果 carmel 无效，不会复制 null
                              let clipboardData = carmel + "\n" + (that.data.classArray[that.data.classIndex].usetip || "");
                              
                              wx.setClipboardData({
                                  data: clipboardData,
                                  success: function success(res) {
                                      that.setData({
                                          shareShow: !1
                                      });
                                  }
                              });
                          }
                      });
                  } else {
                      // 如果请求失败，显示错误消息
                      wx.showModal({
                          title: "提示",
                          content: res.data.msg,
                          showCancel: !1,
                          confirmText: "我知道了",
                          success: function success() {
                              that.setData({
                                  shareShow: !1
                              });
                          }
                      });
                  }
              }).catch(function(res) {
                  wx.showModal({
                      title: "提示",
                      content: "网络请求超时",
                      confirmText: "重试",
                      success: function success() {
                          that.setData({
                              shareShow: !1
                          });
                          that.getClassDatas();
                      }
                  });
              });
          },
          fail: function fail(eer) {
              wx.showModal({
                  title: "提示",
                  content: "网络请求超时",
                  confirmText: "重试",
                  success: function success() {
                      that.setData({
                          shareShow: !1
                      });
                      that.getClassDatas();
                  }
              });
          }
      });
  },
  
  
 

    submitPay: function submitPay() {
        var that = this;
        wx.showLoading({
            title: "正在处理"
        });
        var trade_no = utils.buildOrderNo();
        var content = JSON.stringify({
            order: trade_no,
            cid: this.data.cid,
            name: this.data.classArray[this.data.classIndex].payName
        });
        wx.login({
            success: function success(code) {
                wx.request({
                    url: app.globalData.request_url + "/Api/submit.php",
                    method: "POST",
                    data: {
                        content: content,
                        code: code.code
                    },
                    header: {
                        "content-type": "application/x-www-form-urlencoded; charset=utf-8"
                    },
                    success: function success(res) {
                        console.log(res);
                        if (res.data.code == 200) {
                            wx.hideLoading();
                            // wx.showModal({
                            //     title: '提示',
                            //     content: '订单创建成功,请前往支付。',
                            //     cancelText: "暂不支付",
                            //     confirmColor: '#1AAD19',
                            //     confirmText: "前往支付",
                            //     success(ress) {
                            //         if (ress.confirm) {
                            //             v()
                            //         } else {
                            //         }
                            //     }
                            // })
                                                        wx.requestPayment({
                                //调起支付
                                //下边参数具体看微信小程序官方文档
                                timeStamp: res.data.timeStamp,
                                nonceStr: res.data.nonceStr,
                                package: res.data.package,
                                signType: res.data.signType,
                                paySign: res.data.paySign,
                                success: function success(res) {
                                    console.log("支付成功", res);
                                    that.ok(trade_no);
                                },
                                fail: function fail(eer) {
                                    console.log("支付失败", eer);
                                    wx.showModal({
                                        title: "提示",
                                        content: "支付失败。请重新购买。",
                                        showCancel: !1,
                                        confirmText: "我知道了",
                                        success: function success() {}
                                    });
                                }
                            });
                        } else {
                            wx.hideLoading();
                            wx.showModal({
                                title: "提示",
                                content: res.data.msg,
                                showCancel: !1
                            });
                        }
                    },
                    fail: function fail() {
                        wx.showModal({
                            title: "提示",
                            content: "订单创建失败，请稍后重试。",
                            showCancel: !1
                        });
                    }
                });
            }
        });
    },
    initVideoAd: function initVideoAd(t) {
        var o = this;
        this.openVideoAd(function() {
            t();
        }, function() {
            if (o.data.data.examine == 1) {
                t();
                return;
            }
            wx.showModal({
                title: "使用提示",
                content: o.data.data.adVideoTip,
                showCancel: !1
            });
        }, function() {
            wx.showModal({
                title: "提示",
                content: "您目前暂无广告可看",
                showCancel: !1,
                success: function success(o) {
                    o.confirm && t();
                }
            });
        });
    },
    openVideoAd: function openVideoAd(t, o, a) {
        wx.createRewardedVideoAd ? (wx.showLoading({
            title: "视频加载中"
        }), e && (e.offClose(), e.offError(), e.offLoad()), (e = wx.createRewardedVideoAd({
            adUnitId: this.data.data.adVideoId ? this.data.data.adVideoId : "adunit-3ce72f5fc97170c8"
        })).load().then(function() {
            wx.hideLoading(), e.onClose(function(a) {
                a && a.isEnded ? t && t() : (o && o(), console.log("播放中途退出"));
            }), e.show();
        }).catch(function(t) {
            wx.hideLoading();
        }), e.onLoad(function() {
            wx.hideLoading(), console.log("video 视频加载成功");
        }), e.onError(function(t) {
            wx.hideLoading(), a && a(), console.log(t);
        })) : wx.showModal({
            title: "提示",
            content: "您的微信版本过低，不支持此功能，请升级。"
        });
    },
    onShareAppMessage: function onShareAppMessage() {
        return {
            title: this.data.data.shareTitle ? this.data.data.shareTitle : "这是个神奇的东西",
            path: "/pages/index/index",
            imageUrl: this.data.data.shareImg ? this.data.data.shareImg : "../../img/share.jpg",
            success: function success(t) {
                wx.showToast({
                    title: "分享成功",
                    icon: "success",
                    duration: 2e3
                });
            },
            fail: function fail(t) {
                wx.showToast({
                    title: "分享失败",
                    icon: "none",
                    duration: 2e3
                });
            }
        };
    }
});
