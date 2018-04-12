/**
 * require启动
 * Created on 2015/12/16.
 * @author: 李海波 <lihaibo123as@163.com>
 */
/**
 * 依赖必要类库
 * jquery,angular
 * app.build angular+requirejs 基础构建 返回已经构建完成的angular module实例对象
 * app.ui UI库常用方法
 * FastClick 应对移动短点击300毫秒延迟解决方案,
 * Weui jquery 微信UI库 常用ui组件查看global 插件内 Weui的demos

 */
define(["jquery", "angular", 'app.build', 'app.ui', 'FastClick', 'Weui'], function ($, angular, appIns, uiTool, FastClick) {

        var Custom = {
            app: null,
            injector: null,
            /**
             * app init 初始化
             */
            init: function () {
                var that = this;
                that.app = appIns;
                //that.app.run(['$rootScope',
                //    function ($rootScope) {
                //        $rootScope._G = _G;
                //        if (_G.debug) {
                //            console.debug("rootScope:", $rootScope);
                //        }
                //    }
                //]);
                /**
                 * BaseService 如需引入Require/plugins 的指令插件需依赖当前基础指令
                 * FileService 演示引入文件服务
                 */
                require(['BaseService', 'FileService', 'AMapTool', 'PopupMenuService', 'FileCenter', 'UserTool', 'UiSwiper'], function () {
                    that.initAppModule();
                    that.injector = angular.bootstrap($('body'), ["app"]);
                });
                FastClick.attach(document.body);
            },
            /**
             * 插件引入
             */
            initPlugin: function () {
            },
            /**
             * app module init
             */
            initAppModule: function () {
                //系统初始化
                var that = this;
                that.app.filter('parseHtml', ["$sce", function ($sce) {
                    return function (text) {
                        return $sce.trustAsHtml(text);
                    }
                }])
                    .run(['$rootScope', function ($rootScope) {
                        console.timeEnd('app init');
                        $rootScope.basePath = _G.BASE;
                    }])
                    .controller('testCtrl', ["$scope","$interval", "$element", "$http", "$timeout", "FileService",function ($scope,$interval, $element, $http, $timeout,FileService) {


//-----------------------linw-----------------------------------------------------------------
                        $scope.textcolor='red';
                        $scope.changeColor=function(){
                            if( $scope.textcolor=='red'){
                                $scope.textcolor='blue';
                            }else{
                                $scope.textcolor='red';
                            }

                        }
                        $timeout(function(){
                            $scope.textcolor='pink';
                        },2000);

//---------------------------------------------------------------------------------
                        $scope.nowtime=new Date().toLocaleTimeString();
                        $interval(function(){
                            $scope.nowtime=new Date().toLocaleTimeString();
                        },1000);
//-----------------------------------------------------------------------------------------------
                        var thisTimeObj={
                            showTime:function getNowFormatDate() {
                                var date = new Date();
                                var seperator1 = "-";
                                var seperator2 = ":";
                                var month = date.getMonth() + 1;
                                var strDate = date.getDate();
                                if (month >= 1 && month <= 9) {
                                    month = "0" + month;
                                }
                                if (strDate >= 0 && strDate <= 9) {
                                    strDate = "0" + strDate;
                                }
                                var currentdate = date.getFullYear() + seperator1 + month + seperator1 + strDate
                                    + " " + date.getHours() + seperator2 + date.getMinutes()
                                    + seperator2 + date.getSeconds();
                                return currentdate;
                            }
                        };
                        $scope.nowtimeNew=thisTimeObj.showTime();
                        $interval(function(){
                            $scope.nowtimeNew=thisTimeObj.showTime();
                        },1000);
//------------------------------------------------------------------------------------------------

//------------------------------------------------------------------------------------------------
                        $scope.showinfo=function(){
                            $.actions({
                                title:'选择操作',
                                actions: [
                                    {
                                        text: "查看",
                                        onClick: function() {
                                            $('#showMsg').popup();
                                        }
                                    },{
                                        text: "删除",
                                        onClick: function() {

                                        }
                                    }]
                            });
                        }

//------------------------------------------------------------------------------------------------

                    }]);
            }
        };
        return Custom;
    }
);





