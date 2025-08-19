<?php
// 创建数据库连接
$servername = "localhost";  // 数据库服务器地址
$username = "ziyuanmiao_com";  // 数据库用户名
$password = "Y873eBmC7eYzBJp6";  // 数据库密码
$dbname = "ziyuanmiao_com"; // 数据库名
// 创建连接
$conn = new mysqli($servername, $username, $password, $dbname);
// 检查连接
if ($conn->connect_error) {
die("连接失败: " . $conn->connect_error);
}
// SQL 查询语句
$sql = "SELECT siteUrl, siteUrl FROM bbs_autolink where status = '1'";
// 执行查询
$result = $conn->query($sql);
?>

<!DOCTYPE html>

<html lang="zh"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="QrseG9ZRldpwC5pALWEgSEQk1ntQzhwAxoYos7Cg">
    <title>资源联盟</title>
    <link rel="stylesheet" href="./1_files/wormhole.css">
<input type="hidden" id="_w_simile" data-inspect-config="3">
<script type="text/javascript" src="chrome-extension://dbjbempljhcmhlfpfacalomonjpalpko/scripts/inspector.js"></script></head>
<body>

<div class="container">
    <canvas id="c" width="1920" height="1053"></canvas>

    <div id="content" style="display: flex;">
        <h3>即将奔赴 <b id="name">
        </b> 联盟成员站</h3>
       <!-- <p>您是第 <span id="refer">338</span> 位通过虫洞穿梭到该博客的旅客！<br><span style="color:#606c84">（统计日期始于2022年3月11日）</span></p>
        <div id="vortex">
        </div>-->
        <div class="meta">
            <p>成立时间: <span id="time">2022-06-06</span></p>
            <p id="message">
                <span class="message-left">"</span>
                <span class="text" style="color: rgba(187, 65, 215, 0.95);">网罗世界,汇聚精英;学院联盟,一往无前.</span>
                <span class="message-right">"</span>
            </p>
        </div>
        <div class="footer">
           
            <a href="https://xiu.no/" target="_blank">
               
            
            <div class="time"> ⎛⎝资源联盟⎠⎞  </div>
</a>        </div>
    </div>
</div>






<script src="./1_files/jquery.min.js"></script>
<script>
    "use strict";

    var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

    function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

    var c = document.getElementById('c');
    var w = c.width = window.innerWidth;
    var h = c.height = window.innerHeight;
    var ctx = c.getContext('2d');
    var opts = {
        hexLength: 30,
        lenFn: function lenFn(_ref) {
            var len = _ref.len;
            var t = _ref.t;
            return len + Math.sin(t);
        },
        radFn: function radFn(_ref2) {
            var rad = _ref2.rad;
            var len = _ref2.len;
            var t = _ref2.t;
            var excitement = _ref2.excitement;
            return rad + (excitement + opts.propFn({
                len: len,
                t: t
            })) * 2 / 4;
        },
        propFn: function propFn(_ref3) {
            var len = _ref3.len;
            var t = _ref3.t;
            return len / opts.hexLength / 10 - t;
        },
        excitementFn: function excitementFn(_ref4) {
            var len = _ref4.len;
            var t = _ref4.t;
            return Math.sin(opts.propFn({
                len: len,
                t: t
            })) ** 2;
        },
        colorFn: function colorFn(_ref5) {
            var rad = _ref5.rad;
            var excitement = _ref5.excitement;
            var t = _ref5.t;
            return 'hsl(' + (rad / Math.TAU * 360 + t) + ', ' + excitement * 100 + '%, ' + (20 + excitement * 50) + '%)';
        },
        timeStep: .01,
        randomJig: 8,
        repaintColor: 'rgba(0,0,0,.1)'
    };
    var tick = 0;
    Math.TAU = 6.28318530717958647692;
    var vertices = [];

    var Vertex = function () {
        function Vertex(_ref6) {
            var x = _ref6.x;
            var y = _ref6.y;

            _classCallCheck(this, Vertex);

            this.len = Math.sqrt(x * x + y * y);
            this.rad = Math.acos(x / this.len) * (y > 0 ? 1 : -1) + .13;
            this.prevPoint = {
                x: x,
                y: y
            };
        }

        _createClass(Vertex, [{
            key: 'step',
            value: function step() {
                var excitement = opts.excitementFn({
                    len: this.len,
                    t: tick
                });
                var param = {
                    len: this.len,
                    rad: this.rad,
                    t: tick,
                    excitement: excitement
                };
                var nextLen = opts.lenFn(param);
                var nextRad = opts.radFn(param);
                var color = opts.colorFn(param);
                ctx.strokeStyle = color;
                ctx.lineWidth = excitement + .2;
                ctx.beginPath();
                ctx.moveTo(this.prevPoint.x, this.prevPoint.y);
                this.prevPoint.x = nextLen * Math.cos(nextRad) + Math.random() * (1 - excitement) ** 2 * opts.randomJig * 2 - opts.randomJig;
                this.prevPoint.y = nextLen * Math.sin(nextRad) + Math.random() * (1 - excitement) ** 2 * opts.randomJig * 2 - opts.randomJig;
                ctx.lineTo(this.prevPoint.x, this.prevPoint.y);
                ctx.stroke();
            }
        }], [{
            key: 'gen',
            value: function gen() {
                vertices.length = 0;
                var hexCos = Math.cos(Math.TAU / 12) * opts.hexLength;
                var hexSin = Math.sin(Math.TAU / 12) * opts.hexLength;
                var alternanceX = false;

                for (var x = 0; x < w; x += hexCos) {
                    var alternance = alternanceX = !alternanceX;

                    for (var y = 0; y < h; y += hexSin + opts.hexLength) {
                        alternance = !alternance;
                        vertices.push(new Vertex({
                            x: x - w / 2,
                            y: y + alternance * hexSin - h / 2
                        }));
                    }
                }
            }
        }]);

        return Vertex;
    }();

    Vertex.gen();
    ctx.fillStyle = '#222';
    ctx.fillRect(0, 0, w, h);

    var anim = function anim() {
        window.requestAnimationFrame(anim);
        tick += opts.timeStep;
        ctx.fillStyle = opts.repaintColor;
        ctx.fillRect(0, 0, w, h);
        ctx.translate(w / 2, h / 2);
        vertices.forEach(function (vertex) {
            return vertex.step();
        });
        ctx.translate(-w / 2, -h / 2);
    };

    anim();
    window.addEventListener('resize', function () {
        w = c.width = window.innerWidth;
        h = c.height = window.innerHeight;
        Vertex.gen();
        tick = 0;
        ctx.fillStyle = '#222';
        ctx.fillRect(0, 0, w, h);
    });
</script>


        <script>
    function jumpUrl(){
        var arr = new Array(
            <?php
              while($row = $result->fetch_assoc()) {//遍历数组
             
                   print('"'.$row['siteUrl'].'",');//输出数据表内的链接
                }
                    $conn->close();
            ?>
        );
     window.location.href = arr[Math.floor(Math.random() * arr.length)];
    }
    
    setTimeout(function () {
    jumpUrl();},3666);//设定跳转时间，1s=1000
    </script>





</body></html>