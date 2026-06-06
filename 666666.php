<?php
/**
 * PHP File Browser v17 — Art Deco Emerald
 * PHP 7.4+ · Read-Only · Browse · View · Download
 */

$CFG = array(
    'password'    => 'bela',
    'session_ttl' => 3600,
    'root'        => __DIR__,
    'show_hidden' => false,
    'system_wide' => true,
    'viewable'    => 'txt,php,html,css,js,json,xml,htaccess,md,log,sql,csv,ini,conf,yml,yaml,hpp,cpp,c,h,py,sh,bat',
);

function e17($s) { return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }

function sz17($b) {
    $u = array('B','KB','MB','GB'); $p = 0;
    if ($b > 0) $p = min((int)floor(log($b)/log(1024)), 3);
    return round($b / pow(1024,$p), 1).' '.$u[$p];
}

function ext17($n)   { return strtolower(pathinfo($n, PATHINFO_EXTENSION)); }
function extup17($n) { $e = strtoupper(pathinfo($n, PATHINFO_EXTENSION)); return $e ? $e : '—'; }

function view17($n, $cfg) { return in_array(ext17($n), explode(',', $cfg['viewable'])); }

function color17($n, $d) {
    if ($d) return '#2ECC8A';
    $m = array(
        'php'  => '#A78BFA', 'html' => '#F87171', 'css'  => '#60A5FA',
        'js'   => '#FBBF24', 'json' => '#34D399', 'py'   => '#38BDF8',
        'sql'  => '#F472B6', 'sh'   => '#84CC16', 'md'   => '#94A3B8',
        'log'  => '#FB923C', 'txt'  => '#6B7280', 'yml'  => '#C084FC',
        'yaml' => '#C084FC', 'xml'  => '#F97316', 'csv'  => '#2DD4BF',
    );
    $x = ext17($n);
    return isset($m[$x]) ? $m[$x] : '#6B7280';
}

function clean17($p) {
    return preg_replace('#/+#', '/', str_replace(array('../','..\\'), '', $p));
}

function ok17($p, $cfg) {
    if (!$cfg['system_wide']) {
        $r = realpath($p); $root = realpath($cfg['root']);
        if ($r === false || strpos($r, $root) !== 0) return false;
    }
    return true;
}

function scan17($path, $cfg) {
    if (!is_readable($path)) return array();
    $raw = scandir($path); if (!$raw) return array();
    $out = array();
    foreach ($raw as $n) {
        if ($n === '.') continue;
        if (!$cfg['show_hidden'] && $n[0] === '.' && $n !== '..') continue;
        $f = "$path/$n"; $d = is_dir($f);
        $out[] = array(
            'name' => $n, 'dir'  => $d,
            'size' => $d ? '—' : sz17((int)filesize($f)),
            'date' => date('d M Y', filemtime($f)),
            'time' => date('H:i', filemtime($f)),
            'perms'=> substr(sprintf('%o', fileperms($f)), -4),
            'ext'  => $d ? 'DIR' : extup17($n),
            'clr'  => color17($n, $d),
        );
    }
    usort($out, function($a,$b){
        if ($a['dir'] !== $b['dir']) return $b['dir'] ? 1 : -1;
        return strcmp($a['name'], $b['name']);
    });
    return $out;
}

function crumbs17($path) {
    $bc = array(); $cur = '';
    foreach (array_filter(explode('/', str_replace('\\','/',$path))) as $p) {
        $cur .= "/$p"; $bc[] = array('name'=>$p,'path'=>$cur);
    }
    return $bc;
}

function udirs17() {
    $d = array();
    if (!is_dir('/home') || !is_readable('/home')) return $d;
    $s = @scandir('/home'); if (!$s) return $d;
    foreach ($s as $i)
        if ($i !== '.' && $i !== '..' && is_dir("/home/$i")) $d[] = "/home/$i";
    return $d;
}

function sess17($cfg) {
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_only_cookies', 1);
        session_start();
    }
    if (isset($_SESSION['_t']) && (time() - $_SESSION['_t']) > $cfg['session_ttl']) {
        session_unset(); session_destroy(); session_start();
    }
    $_SESSION['_t'] = time();
}

function authed17()   { return isset($_SESSION['_ok']) && $_SESSION['_ok'] === true; }
function login17($pw, $cfg) {
    if ($pw !== $cfg['password']) return false;
    $_SESSION['_ok'] = true; $_SESSION['_s'] = time(); $_SESSION['_t'] = time();
    return true;
}
function logout17()   { session_unset(); session_destroy(); }
function age17()      { $t = isset($_SESSION['_s']) ? $_SESSION['_s'] : time(); return gmdate('H:i:s', time()-$t); }

function head17($title) {
    echo '<!DOCTYPE html><html lang="en"><head>';
    echo '<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">';
    echo '<title>'.e17($title).'</title>';
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">';
    echo '<link href="https://fonts.googleapis.com/css2?family=Cinzel+Decorative:wght@400;700;900&family=DM+Mono:ital,wght@0,300;0,400;1,300&display=swap" rel="stylesheet">';
    echo '
<style>
:root{
    --blk:   #06080C;
    --blk2:  #080C12;
    --blk3:  #0C1018;
    --blk4:  #101520;
    --blk5:  #141C28;
    --blk6:  #1A2234;
    --em:    #10B981;
    --em2:   #34D399;
    --em3:   #6EE7B7;
    --em-d:  rgba(16,185,129,0.10);
    --em-m:  rgba(16,185,129,0.22);
    --em-b:  rgba(16,185,129,0.45);
    --gld:   #D4AF37;
    --gld2:  #E8C84C;
    --gld3:  #F5DC80;
    --gld-d: rgba(212,175,55,0.12);
    --gld-m: rgba(212,175,55,0.28);
    --gld-b: rgba(212,175,55,0.50);
    --red:   #EF4444;
    --red-d: rgba(239,68,68,0.10);
    --bdr:   rgba(212,175,55,0.18);
    --bdr2:  rgba(212,175,55,0.30);
    --bdr3:  rgba(212,175,55,0.50);
    --txt:   #C8D8C0;
    --txt2:  #90A890;
    --txt3:  #5A6A58;
    --txt4:  #3A4438;
    --dis:   "Cinzel Decorative",serif;
    --mono:  "DM Mono",monospace;
}
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
html,body{height:100%;overflow:hidden;}
body{font-family:var(--mono);background:var(--blk);color:var(--txt);}
</style>';
}

/* BOOT */
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
sess17($CFG);
if (isset($_GET['logout'])) { logout17(); header('Location: '.$_SERVER['PHP_SELF']); exit; }

/* ═══════════════════════════════════
   LOGIN
═══════════════════════════════════ */
if (!authed17()) {
    $err = false;
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pw'])) {
        if (login17($_POST['pw'], $CFG)) { header('Location: '.$_SERVER['PHP_SELF']); exit; }
        $err = true;
    }
    head17('File Browser — Enter');
?>
<style>
body{
    display:flex;align-items:center;justify-content:center;
    min-height:100vh;overflow:hidden;position:relative;
    background:var(--blk);
}

/* Art Deco geometric background */
.deco-bg{
    position:fixed;inset:0;pointer-events:none;overflow:hidden;
}

/* Corner fan ornaments */
.fan{
    position:absolute;
    width:320px;height:320px;
    border-radius:0;
    opacity:.06;
}
.fan::before,.fan::after{
    content:'';position:absolute;
    border-radius:50%;
    border:1px solid var(--gld);
}
.fan-tl{top:-80px;left:-80px;}
.fan-tr{top:-80px;right:-80px;transform:scaleX(-1);}
.fan-bl{bottom:-80px;left:-80px;transform:scaleY(-1);}
.fan-br{bottom:-80px;right:-80px;transform:scale(-1);}
.fan::before{width:200%;height:200%;top:-50%;left:-50%;}
.fan::after{width:180%;height:180%;top:-40%;left:-40%;}

/* Geometric pattern lines */
.geo-lines{
    position:fixed;inset:0;pointer-events:none;
    background-image:
        repeating-linear-gradient(45deg, rgba(212,175,55,0.03) 0px, rgba(212,175,55,0.03) 1px, transparent 1px, transparent 40px),
        repeating-linear-gradient(-45deg, rgba(212,175,55,0.02) 0px, rgba(212,175,55,0.02) 1px, transparent 1px, transparent 40px);
}

/* Vertical gold lines */
.v-lines{
    position:fixed;inset:0;pointer-events:none;
    background-image:
        linear-gradient(90deg, transparent 19.5%, rgba(212,175,55,0.04) 19.5%, rgba(212,175,55,0.04) 20%, transparent 20%),
        linear-gradient(90deg, transparent 79.5%, rgba(212,175,55,0.04) 79.5%, rgba(212,175,55,0.04) 80%, transparent 80%);
}

/* Centre diamond ornament */
.centre-diamond{
    position:fixed;top:50%;left:50%;
    transform:translate(-50%,-50%) rotate(45deg);
    width:600px;height:600px;
    border:1px solid rgba(212,175,55,0.04);
    pointer-events:none;
}
.centre-diamond::before{
    content:'';position:absolute;inset:40px;
    border:1px solid rgba(212,175,55,0.03);
}

.shell{
    position:relative;z-index:10;
    width:480px;
    animation:decoIn .7s cubic-bezier(.34,1.08,.64,1) both;
}
@keyframes decoIn{
    from{opacity:0;transform:translateY(28px);}
    to{opacity:1;transform:translateY(0);}
}

/* Top ornamental band */
.orn-band{
    text-align:center;margin-bottom:0;
    padding:0 0 20px;
    position:relative;
}
/* Horizontal ruled lines */
.ruled{
    display:flex;align-items:center;gap:0;
    margin-bottom:8px;
}
.ruled-line{flex:1;height:1px;background:var(--gld);opacity:.35;}
.ruled-diamond{
    width:8px;height:8px;
    background:var(--gld);
    transform:rotate(45deg);
    flex-shrink:0;margin:0 12px;opacity:.6;
}
.ruled-sm-line{width:24px;height:1px;background:var(--gld);opacity:.25;flex-shrink:0;}

.orn-label{
    font-family:var(--dis);font-size:8px;letter-spacing:6px;
    text-transform:uppercase;color:var(--gld);opacity:.5;
    margin-bottom:4px;
}
.orn-num{
    font-family:var(--mono);font-size:9px;color:var(--txt3);
    letter-spacing:3px;
}

/* Main title panel */
.title-panel{
    background:var(--blk2);
    border:1px solid var(--bdr2);
    padding:0;
    position:relative;
    overflow:hidden;
}

/* Gold corner L-marks */
.title-panel::before,.title-panel::after{
    content:'';position:absolute;
    width:20px;height:20px;
    border-color:var(--gld);border-style:solid;
    opacity:.5;z-index:2;
}
.title-panel::before{top:0;left:0;border-width:2px 0 0 2px;}
.title-panel::after{bottom:0;right:0;border-width:0 2px 2px 0;}

/* Emerald accent line at very top */
.em-rule{
    height:2px;
    background:linear-gradient(90deg,transparent,var(--em),var(--gld),var(--em),transparent);
}

/* Masthead area */
.masthead{
    padding:32px 36px 28px;
    text-align:center;
    position:relative;
    border-bottom:1px solid var(--bdr);
}

/* Top inner corner marks */
.masthead::before,.masthead::after{
    content:'';position:absolute;
    width:12px;height:12px;
    border-color:var(--gld);border-style:solid;opacity:.4;
}
.masthead::before{top:12px;left:12px;border-width:1px 0 0 1px;}
.masthead::after{top:12px;right:12px;border-width:1px 1px 0 0;}

.m-eyebrow{
    font-family:var(--mono);font-size:9px;letter-spacing:5px;
    text-transform:uppercase;color:var(--em);opacity:.65;
    margin-bottom:12px;
}
.m-title{
    font-family:var(--dis);font-size:38px;font-weight:900;
    line-height:.95;letter-spacing:2px;
    color:var(--gld2);
    text-shadow:0 0 40px rgba(212,175,55,0.25);
    margin-bottom:4px;
}
.m-title span{
    display:block;font-size:22px;font-weight:400;
    color:var(--em3);letter-spacing:6px;
    text-shadow:0 0 20px rgba(110,231,183,0.2);
}
.m-sub{
    font-family:var(--mono);font-size:9px;color:var(--txt3);
    letter-spacing:3px;text-transform:uppercase;margin-top:10px;
}

/* Form area */
.form-area{padding:28px 36px 0;}

.err-strip{
    border:1px solid rgba(239,68,68,.25);border-left:3px solid var(--red);
    background:var(--red-d);
    padding:10px 14px;font-size:12px;color:var(--red);
    margin-bottom:18px;letter-spacing:.3px;
}

.f-lbl{
    font-family:var(--dis);font-size:8px;letter-spacing:4px;
    text-transform:uppercase;color:var(--gld);opacity:.6;
    display:block;margin-bottom:10px;
}

.f-row{display:flex;margin-bottom:28px;}
.f-row input{
    flex:1;padding:13px 18px;
    background:var(--blk);
    border:1px solid var(--bdr2);border-right:none;
    color:var(--em2);font-family:var(--mono);font-size:15px;
    letter-spacing:4px;outline:none;transition:all .18s;
    caret-color:var(--em);
}
.f-row input:focus{
    border-color:var(--em-m);background:var(--blk2);
    box-shadow:0 0 0 1px var(--em-d),0 0 16px var(--em-d);
}
.f-row input::placeholder{color:var(--txt4);font-size:13px;letter-spacing:2px;}
.f-row button{
    padding:13px 24px;
    background:var(--em);border:1px solid var(--em);
    color:var(--blk);font-family:var(--dis);font-size:13px;
    font-weight:700;letter-spacing:2px;
    cursor:pointer;transition:all .16s;white-space:nowrap;
}
.f-row button:hover{
    background:var(--em2);
    box-shadow:0 0 20px rgba(16,185,129,.35);
}

/* Bottom data band */
.data-band{
    display:grid;grid-template-columns:1fr 1fr 1fr;
    border-top:1px solid var(--bdr);
}
.db-cell{
    padding:12px 14px;text-align:center;
    border-right:1px solid var(--bdr);
}
.db-cell:last-child{border-right:none;}
.db-k{
    font-family:var(--dis);font-size:7px;letter-spacing:2px;
    text-transform:uppercase;color:var(--gld);opacity:.4;
    margin-bottom:4px;
}
.db-v{font-family:var(--mono);font-size:11px;color:var(--txt2);letter-spacing:.5px;}
.db-v.hi{color:var(--em2);}

/* Bottom ornament */
.bot-orn{
    text-align:center;padding:16px 0 0;
}
.bot-ruled{display:flex;align-items:center;gap:0;}
.bot-ruled-line{flex:1;height:1px;background:var(--gld);opacity:.2;}
.bot-ruled-dot{width:4px;height:4px;background:var(--em);border-radius:50%;margin:0 10px;opacity:.5;}
</style>
</head>
<body>

<div class="deco-bg">
    <div class="fan fan-tl"></div>
    <div class="fan fan-tr"></div>
    <div class="fan fan-bl"></div>
    <div class="fan fan-br"></div>
</div>
<div class="geo-lines"></div>
<div class="v-lines"></div>
<div class="centre-diamond"></div>

<div class="shell">
    <div class="orn-band">
        <div class="ruled">
            <div class="ruled-line"></div>
            <div class="ruled-sm-line"></div>
            <div class="ruled-diamond"></div>
            <div class="ruled-sm-line"></div>
            <div class="ruled-diamond"></div>
            <div class="ruled-sm-line"></div>
            <div class="ruled-line"></div>
        </div>
        <div class="orn-label">File System Access</div>
        <div class="orn-num">Est. v.XVII · Read Only</div>
    </div>

    <div class="title-panel">
        <div class="em-rule"></div>
        <div class="masthead">
            <p class="m-eyebrow">Observatory Console</p>
            <div class="m-title">
                File
                <span>Browser</span>
            </div>
            <p class="m-sub">Authenticate to enter</p>
        </div>
        <div class="form-area">
            <?php if ($err): ?>
            <div class="err-strip">Access denied — invalid credentials</div>
            <?php endif; ?>
            <label class="f-lbl">Access Passphrase</label>
            <form method="POST">
                <div class="f-row">
                    <input type="password" name="pw" placeholder="· · · · · · · ·" required autofocus>
                    <button type="submit">Enter</button>
                </div>
            </form>
        </div>
        <div class="data-band">
            <div class="db-cell"><div class="db-k">Mode</div><div class="db-v hi">Read Only</div></div>
            <div class="db-cell"><div class="db-k">Session</div><div class="db-v">60 min</div></div>
            <div class="db-cell"><div class="db-k">Enc</div><div class="db-v">Secure</div></div>
        </div>
    </div>

    <div class="bot-orn">
        <div class="bot-ruled">
            <div class="bot-ruled-line"></div>
            <div class="bot-ruled-dot"></div>
            <div class="bot-ruled-line"></div>
        </div>
    </div>
</div>
</body></html>
<?php exit; }

/* ── PATH ── */
$cwd = realpath($CFG['root']);
if (isset($_GET['path'])) {
    $rp = clean17($_GET['path']);
    $ck = ($rp !== '' && $rp[0] === '/') ? $rp : $CFG['root'].'/'.$rp;
    if (is_dir($ck) && ok17($ck, $CFG)) $cwd = realpath($ck);
}

/* ── DOWNLOAD ── */
if (isset($_GET['download'])) {
    $df = $cwd.'/'.basename($_GET['download']);
    if (file_exists($df) && is_file($df)) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($df).'"');
        header('Content-Length: '.filesize($df));
        readfile($df); exit;
    }
}

/* ═══════════════════════════════════
   VIEWER
═══════════════════════════════════ */
if (isset($_GET['view'])) {
    $vf = $cwd.'/'.basename($_GET['view']);
    if (file_exists($vf) && is_file($vf) && view17($vf, $CFG)) {
        $fc    = file_get_contents($vf);
        $fsz   = sz17((int)filesize($vf));
        $fdt   = date('d M Y · H:i', filemtime($vf));
        $fext  = extup17($vf);
        $fcol  = color17($vf, false);
        $fline = substr_count($fc, "\n") + 1;
        $back  = e17($_SERVER['PHP_SELF'].'?path='.urlencode(dirname($vf)));
        head17(basename($vf).' · View');
?>
<style>
html,body{height:100%;overflow:hidden;}
body{display:flex;flex-direction:column;background:var(--blk);}

/* Art Deco bg lines for viewer */
body::before{
    content:'';position:fixed;inset:0;pointer-events:none;z-index:0;
    background-image:
        repeating-linear-gradient(45deg, rgba(212,175,55,0.015) 0px, rgba(212,175,55,0.015) 1px, transparent 1px, transparent 50px),
        repeating-linear-gradient(-45deg, rgba(212,175,55,0.01) 0px, rgba(212,175,55,0.01) 1px, transparent 1px, transparent 50px);
}

.vbar{
    height:54px;flex-shrink:0;position:relative;z-index:10;
    background:var(--blk2);
    border-bottom:1px solid var(--bdr2);
    display:flex;align-items:stretch;
}
.vbar::before{
    content:'';position:absolute;top:0;left:0;right:0;height:2px;
    background:linear-gradient(90deg,transparent,var(--em),var(--gld),var(--em),transparent);
}

.vb-back{
    display:flex;align-items:center;gap:7px;padding:0 18px;
    border-right:1px solid var(--bdr);
    color:var(--txt3);text-decoration:none;
    font-family:var(--dis);font-size:9px;letter-spacing:2px;
    text-transform:uppercase;transition:all .14s;flex-shrink:0;
}
.vb-back:hover{color:var(--em2);background:var(--em-d);}

.vb-ext{
    display:flex;align-items:center;padding:0 16px;
    border-right:1px solid var(--bdr);
    font-family:var(--dis);font-size:11px;letter-spacing:2px;
    font-weight:700;flex-shrink:0;
}

.vb-name{
    display:flex;align-items:center;padding:0 20px;flex:1;min-width:0;
    font-family:var(--dis);font-size:14px;font-weight:700;letter-spacing:1px;
    color:var(--gld2);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
}

.vb-meta{
    display:flex;align-items:center;gap:18px;padding:0 18px;
    border-left:1px solid var(--bdr);flex-shrink:0;
    font-size:10px;color:var(--txt3);letter-spacing:.5px;
}

.vb-dl{
    display:flex;align-items:center;gap:7px;padding:0 20px;
    border-left:1px solid var(--bdr);
    background:var(--em-d);
    color:var(--em2);font-family:var(--dis);
    font-size:10px;letter-spacing:2px;text-transform:uppercase;
    text-decoration:none;transition:all .14s;flex-shrink:0;
}
.vb-dl:hover{background:var(--em-m);color:var(--em3);}

.viewer{flex:1;display:flex;overflow:hidden;position:relative;z-index:1;}
.gutter{
    background:var(--blk3);border-right:1px solid var(--bdr);
    padding:16px 14px;font-family:var(--mono);font-size:12px;
    color:var(--txt4);text-align:right;min-width:52px;
    overflow:hidden;white-space:pre;line-height:1.75;user-select:none;
}
.code-view{
    flex:1;overflow:auto;padding:16px 24px;
    font-family:var(--mono);font-size:13px;line-height:1.75;
    color:var(--txt);white-space:pre;tab-size:4;background:var(--blk);
}
.code-view::-webkit-scrollbar{width:6px;height:6px;}
.code-view::-webkit-scrollbar-thumb{background:var(--blk6);border-radius:2px;}

.vfoot{
    height:32px;flex-shrink:0;position:relative;z-index:10;
    background:var(--blk2);border-top:1px solid var(--bdr);
    display:flex;align-items:center;padding:0 22px;gap:22px;
    font-family:var(--dis);font-size:8px;color:var(--txt3);
    letter-spacing:2px;text-transform:uppercase;
}
.vfoot strong{color:var(--em2);}
.ro-stamp{
    margin-left:auto;
    border:1px solid var(--bdr3);color:var(--gld);
    font-size:8px;letter-spacing:3px;padding:2px 10px;
    background:var(--gld-d);
}
</style>
</head>
<body>
<div class="vbar">
    <a href="<?php echo $back; ?>" class="vb-back">← Back</a>
    <div class="vb-ext" style="color:<?php echo $fcol; ?>;"><?php echo e17($fext); ?></div>
    <div class="vb-name"><?php echo e17(basename($vf)); ?></div>
    <div class="vb-meta">
        <span><?php echo e17($fsz); ?></span>
        <span><?php echo $fline; ?> lines</span>
        <span><?php echo e17($fdt); ?></span>
    </div>
    <a href="?path=<?php echo urlencode($cwd); ?>&download=<?php echo urlencode(basename($vf)); ?>" class="vb-dl">↓ Download</a>
</div>
<div class="viewer">
    <div class="gutter"><?php echo implode("\n", range(1, $fline)); ?></div>
    <pre class="code-view"><?php echo e17($fc); ?></pre>
</div>
<div class="vfoot">
    <span>Ln <strong><?php echo $fline; ?></strong></span>
    <span>Sz <strong><?php echo e17($fsz); ?></strong></span>
    <span>Md <strong><?php echo e17(substr(sprintf('%o', fileperms($vf)), -4)); ?></strong></span>
    <span>UTF-8</span>
    <div class="ro-stamp">Read Only</div>
</div>
</body></html>
<?php exit; } }

/* ═══════════════════════════════════
   MAIN BROWSER
═══════════════════════════════════ */
$items  = scan17($cwd, $CFG);
$bc     = crumbs17($cwd);
$udirs  = udirs17();
$ndirs  = count(array_filter($items, function($i){ return $i['dir']; }));
$nfiles = count(array_filter($items, function($i){ return !$i['dir']; }));
$age    = age17();
$who    = get_current_user();

head17('FB · '.(basename($cwd) ? basename($cwd) : '/'));
?>
<style>
html,body{height:100%;overflow:hidden;}
body{display:flex;flex-direction:column;}

/* Art Deco diagonal grid */
body::before{
    content:'';position:fixed;inset:0;pointer-events:none;z-index:0;
    background-image:
        repeating-linear-gradient(45deg,rgba(212,175,55,0.02) 0,rgba(212,175,55,0.02) 1px,transparent 1px,transparent 44px),
        repeating-linear-gradient(-45deg,rgba(212,175,55,0.015) 0,rgba(212,175,55,0.015) 1px,transparent 1px,transparent 44px);
}

/* ── TOPBAR ── */
.topbar{
    height:54px;flex-shrink:0;position:relative;z-index:100;
    background:var(--blk2);
    border-bottom:1px solid var(--bdr2);
    display:flex;align-items:stretch;
}
.topbar::before{
    content:'';position:absolute;top:0;left:0;right:0;height:2px;
    background:linear-gradient(90deg,transparent,var(--em),var(--gld2),var(--em),transparent);
}

.tb-brand{
    display:flex;align-items:center;gap:14px;
    padding:0 24px;border-right:1px solid var(--bdr);flex-shrink:0;
}
.tb-logo{
    font-family:var(--dis);font-size:16px;font-weight:900;
    letter-spacing:3px;color:var(--gld2);
    text-shadow:0 0 20px rgba(212,175,55,0.2);
}
.tb-badge{
    font-family:var(--dis);font-size:8px;letter-spacing:2px;
    color:var(--em);border:1px solid var(--em-m);padding:2px 8px;
    background:var(--em-d);text-transform:uppercase;
}

.tb-path{
    flex:1;display:flex;align-items:center;
    padding:0 20px;border-right:1px solid var(--bdr);
    font-size:11px;color:var(--txt3);
    white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
}
.tb-path strong{color:var(--txt2);}

.tb-right{display:flex;align-items:stretch;}

.tb-stat{
    display:flex;flex-direction:column;align-items:center;
    justify-content:center;padding:0 16px;border-right:1px solid var(--bdr);
}
.tb-stat .sn{font-family:var(--dis);font-size:18px;font-weight:900;line-height:1;letter-spacing:1px;}
.tb-stat .sl{font-family:var(--dis);font-size:7px;letter-spacing:2px;text-transform:uppercase;color:var(--txt3);margin-top:2px;}
.tb-stat.d .sn{color:var(--em2);}
.tb-stat.f .sn{color:var(--gld2);}

.tb-meta{
    padding:0 16px;border-right:1px solid var(--bdr);
    display:flex;flex-direction:column;justify-content:center;font-size:11px;gap:2px;
}
.tb-meta small{color:var(--txt3);}
.tb-meta strong{color:var(--txt2);font-weight:400;}

.tb-out{
    display:flex;align-items:center;padding:0 20px;
    font-family:var(--dis);font-size:10px;letter-spacing:2px;
    text-transform:uppercase;color:var(--txt3);
    text-decoration:none;transition:color .14s;
}
.tb-out:hover{color:var(--red);}

/* ── LAYOUT ── */
.layout{display:flex;flex:1;overflow:hidden;position:relative;z-index:1;}

/* ── SIDEBAR ── */
.sidebar{
    width:222px;min-width:222px;
    background:var(--blk2);
    border-right:1px solid var(--bdr2);
    display:flex;flex-direction:column;overflow:hidden;
    position:relative;z-index:10;
}
/* Gold-emerald gradient left line */
.sidebar::before{
    content:'';position:absolute;top:0;left:0;bottom:0;width:2px;
    background:linear-gradient(180deg,var(--em) 0%,var(--gld) 50%,rgba(212,175,55,0.1) 100%);
    opacity:.6;
}

.sb-head{
    padding:18px 16px 16px 20px;
    border-bottom:1px solid var(--bdr);
    background:rgba(12,16,24,0.5);
}
.sb-sec{
    font-family:var(--dis);font-size:8px;letter-spacing:4px;
    text-transform:uppercase;color:var(--gld);opacity:.5;
    margin-bottom:12px;display:flex;align-items:center;gap:8px;
}
.sb-sec::after{content:'';flex:1;height:1px;background:var(--gld);opacity:.2;}

.sb-stats{display:grid;grid-template-columns:1fr 1fr;gap:6px;margin-bottom:14px;}
.sbs{
    background:var(--blk3);border:1px solid var(--bdr);
    padding:9px 10px;text-align:center;position:relative;overflow:hidden;
}
.sbs::after{content:'';position:absolute;bottom:0;left:0;right:0;height:1px;}
.sbs.d::after{background:var(--em);box-shadow:0 0 4px var(--em);}
.sbs.f::after{background:var(--gld);opacity:.5;}
.sbs .sn{font-family:var(--dis);font-size:20px;font-weight:900;line-height:1;margin-bottom:3px;}
.sbs.d .sn{color:var(--em2);}
.sbs.f .sn{color:var(--gld2);}
.sbs .sl{font-family:var(--dis);font-size:7px;letter-spacing:2px;text-transform:uppercase;color:var(--txt3);}

.sb-path{
    font-size:10px;color:var(--txt3);line-height:1.5;word-break:break-all;
    border-left:2px solid var(--em);padding-left:8px;opacity:.8;
}

.sb-nav{flex:1;overflow-y:auto;padding:8px 0;}
.sb-nav::-webkit-scrollbar{width:2px;}
.sb-nav::-webkit-scrollbar-thumb{background:var(--blk6);}

.sb-grp{
    padding:10px 20px 3px;
    font-family:var(--dis);font-size:7px;letter-spacing:3px;
    text-transform:uppercase;color:var(--txt4);
}

.sb-btn{
    display:flex;align-items:center;gap:9px;
    padding:9px 20px;font-size:12px;color:var(--txt2);
    text-decoration:none;border-left:2px solid transparent;
    transition:all .12s;letter-spacing:.3px;
    background:none;border-top:none;border-right:none;border-bottom:none;
    width:100%;font-family:var(--mono);cursor:pointer;text-align:left;
}
.sb-btn:hover{color:var(--txt);background:var(--blk3);border-left-color:var(--txt4);}
.sb-btn.active{color:var(--em2);background:var(--em-d);border-left-color:var(--em);}
.sb-btn .stag{margin-left:auto;font-family:var(--dis);font-size:7px;letter-spacing:1px;color:var(--txt4);}
.sb-btn.active .stag{color:var(--em-m);}

/* ── MAIN ── */
.main{flex:1;display:flex;flex-direction:column;overflow:hidden;min-width:0;}

/* ── NAVSTRIP ── */
.navstrip{
    height:44px;flex-shrink:0;position:relative;z-index:10;
    background:var(--blk3);
    border-bottom:1px solid var(--bdr);
    display:flex;align-items:center;padding:0 20px;gap:12px;
}

.breadcrumb{flex:1;display:flex;align-items:center;overflow:hidden;min-width:0;}
.breadcrumb a{
    font-size:12px;color:var(--txt3);text-decoration:none;
    padding:3px 7px;transition:all .12s;white-space:nowrap;
}
.breadcrumb a:hover{color:var(--em2);background:var(--em-d);}
.breadcrumb a:last-child{color:var(--txt);}
.bc-sep{color:var(--blk6);padding:0 2px;font-family:var(--mono);}

.path-form{display:flex;}
.path-form input{
    padding:7px 13px;background:var(--blk);
    border:1px solid var(--blk6);border-right:none;
    color:var(--txt);font-family:var(--mono);font-size:11px;
    outline:none;width:216px;transition:border-color .14s;caret-color:var(--em);
}
.path-form input:focus{border-color:var(--em-m);}
.path-form input::placeholder{color:var(--txt4);}
.path-form button{
    padding:7px 15px;background:var(--em);border:1px solid var(--em);
    color:var(--blk);font-family:var(--dis);font-size:11px;
    font-weight:700;letter-spacing:2px;text-transform:uppercase;
    cursor:pointer;transition:all .14s;
}
.path-form button:hover{background:var(--em2);box-shadow:0 0 12px rgba(16,185,129,.3);}

/* ── FILE AREA ── */
.file-area{flex:1;overflow-y:auto;position:relative;z-index:5;}
.file-area::-webkit-scrollbar{width:5px;}
.file-area::-webkit-scrollbar-thumb{background:var(--blk6);border-radius:2px;}

table{width:100%;border-collapse:collapse;}

thead{position:sticky;top:0;z-index:10;}
thead th{
    padding:10px 16px;text-align:left;
    font-family:var(--dis);font-size:8px;letter-spacing:3px;text-transform:uppercase;
    color:var(--txt3);background:var(--blk2);
    border-bottom:1px solid var(--bdr2);font-weight:400;
}
thead th:first-child{border-left:2px solid var(--em);padding-left:14px;color:var(--em);}
thead th:last-child{background:var(--em-d);color:var(--em);}

tbody tr{border-bottom:1px solid rgba(26,34,52,0.8);transition:background .08s;}
tbody tr:last-child{border-bottom:none;}
tbody tr:hover{background:var(--blk3);}
tbody tr:hover td:first-child{border-left-color:var(--em);}

td{padding:10px 16px;vertical-align:middle;}
td:first-child{padding-left:14px;border-left:2px solid transparent;transition:border-color .12s;}

/* Colored dot + text ext style */
.ext-dot-row{display:inline-flex;align-items:center;gap:7px;}
.ext-dot{width:7px;height:7px;border-radius:50%;flex-shrink:0;}
.ext-text{
    font-family:var(--dis);font-size:9px;letter-spacing:1px;
    font-weight:700;text-transform:uppercase;
}

.name-cell{display:flex;align-items:center;gap:10px;}
.iname{font-size:13px;color:var(--txt);}
.iname a{color:inherit;text-decoration:none;transition:color .12s;}
.iname a:hover{color:var(--em2);}
.iname a.isdir{
    font-family:var(--dis);font-size:13px;font-weight:700;
    letter-spacing:.5px;color:var(--gld2);
}
.iname a.isdir:hover{color:var(--em2);}

td.sz{font-size:11px;color:var(--txt3);width:82px;}
td.dt{font-size:11px;color:var(--txt3);width:116px;}
td.dt .t{font-size:10px;color:var(--txt4);margin-top:1px;}
td.pm{font-family:var(--dis);font-size:9px;color:var(--txt4);width:56px;letter-spacing:.5px;}
td.ac{width:150px;}

.row-acts{display:flex;gap:6px;opacity:0;transition:opacity .12s;}
tr:hover .row-acts{opacity:1;}

.ra{
    padding:4px 12px;
    border:1px solid var(--blk6);background:transparent;
    color:var(--txt3);font-family:var(--dis);
    font-size:9px;letter-spacing:2px;text-transform:uppercase;
    cursor:pointer;text-decoration:none;
    display:inline-flex;align-items:center;gap:5px;
    transition:all .12s;
}
.ra-view:hover{
    border-color:var(--em-b);color:var(--em2);background:var(--em-d);
    box-shadow:0 0 10px rgba(16,185,129,.12);
}
.ra-dl:hover{
    border-color:var(--gld-b);color:var(--gld2);background:var(--gld-d);
    box-shadow:0 0 10px rgba(212,175,55,.12);
}

/* Empty state */
.empty{text-align:center;padding:80px;color:var(--txt4);}
.empty-sym{
    font-family:var(--dis);font-size:44px;font-weight:900;letter-spacing:6px;
    color:var(--blk5);margin-bottom:14px;
    text-shadow:0 0 20px rgba(16,185,129,.05);
}
.empty p{font-family:var(--dis);font-size:8px;letter-spacing:4px;text-transform:uppercase;color:var(--txt4);}

@keyframes rowIn{from{opacity:0;transform:translateX(-5px);}to{opacity:1;transform:translateX(0);}}
</style>
</head>
<body>

<!-- TOPBAR -->
<div class="topbar">
    <div class="tb-brand">
        <span class="tb-logo">File Browser</span>
        <span class="tb-badge">v. XVII</span>
    </div>
    <div class="tb-path"><strong><?php echo e17($cwd); ?></strong></div>
    <div class="tb-right">
        <div class="tb-stat d"><div class="sn"><?php echo $ndirs; ?></div><div class="sl">Dirs</div></div>
        <div class="tb-stat f"><div class="sn"><?php echo $nfiles; ?></div><div class="sl">Files</div></div>
        <div class="tb-meta">
            <strong><?php echo e17($who); ?></strong>
            <small><?php echo $age; ?></small>
        </div>
        <a href="?logout" class="tb-out">Leave</a>
    </div>
</div>

<div class="layout">

<!-- SIDEBAR -->
<aside class="sidebar">
    <div class="sb-head">
        <div class="sb-sec">Location</div>
        <div class="sb-stats">
            <div class="sbs d"><div class="sn"><?php echo $ndirs; ?></div><div class="sl">Dirs</div></div>
            <div class="sbs f"><div class="sn"><?php echo $nfiles; ?></div><div class="sl">Files</div></div>
        </div>
        <div class="sb-path"><?php echo e17($cwd); ?></div>
    </div>
    <nav class="sb-nav">
        <div class="sb-grp">Navigate</div>
        <?php
        $navs = array(
            array('/', 'Root', '/'),
            array('/home', 'Home', '/home'),
            array($CFG['root'], 'Script', 'cwd'),
        );
        foreach ($udirs as $ud) { $navs[] = array($ud, basename($ud), 'usr'); }
        foreach ($navs as $nav) {
            $v = $nav[0]; $l = $nav[1]; $t = $nav[2];
            $cls = (realpath($v) === $cwd) ? 'active' : '';
            echo '<form method="GET" style="display:contents;">';
            echo '<button type="submit" name="path" value="'.e17($v).'" class="sb-btn '.$cls.'">';
            echo e17($l).'<span class="stag">'.e17($t).'</span>';
            echo '</button></form>';
        }
        ?>
    </nav>
</aside>

<!-- MAIN -->
<main class="main">
    <div class="navstrip">
        <nav class="breadcrumb">
            <a href="?path=/">~</a>
            <?php foreach ($bc as $c): ?>
                <span class="bc-sep">/</span>
                <a href="?path=<?php echo urlencode($c['path']); ?>"><?php echo e17($c['name']); ?></a>
            <?php endforeach; ?>
        </nav>
        <form method="GET" class="path-form">
            <input type="text" name="path" placeholder="/navigate/to/path" value="<?php echo e17($cwd); ?>">
            <button type="submit">Go</button>
        </form>
    </div>

    <div class="file-area">
        <?php if (empty($items)): ?>
        <div class="empty">
            <div class="empty-sym">∅</div>
            <p>Empty Directory</p>
        </div>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Name</th>
                    <th>Size</th>
                    <th>Modified</th>
                    <th>Mode</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($items as $idx => $item): ?>
            <tr style="animation:rowIn .2s ease both;animation-delay:<?php echo min($idx*0.014, 0.28); ?>s;">
                <td>
                    <div class="ext-dot-row">
                        <div class="ext-dot" style="background:<?php echo $item['clr']; ?>;box-shadow:0 0 5px <?php echo $item['clr']; ?>66;"></div>
                        <span class="ext-text" style="color:<?php echo $item['clr']; ?>;">
                            <?php $e=$item['ext']; echo e17(strlen($e)>4?substr($e,0,4):$e); ?>
                        </span>
                    </div>
                </td>
                <td>
                    <div class="name-cell">
                        <span class="iname">
                            <?php if ($item['dir']): ?>
                                <a href="?path=<?php echo urlencode($cwd.'/'.$item['name']); ?>" class="isdir"><?php echo e17($item['name']); ?></a>
                            <?php else: ?>
                                <a><?php echo e17($item['name']); ?></a>
                            <?php endif; ?>
                        </span>
                    </div>
                </td>
                <td class="sz"><?php echo e17($item['size']); ?></td>
                <td class="dt"><?php echo e17($item['date']); ?><div class="t"><?php echo e17($item['time']); ?></div></td>
                <td class="pm"><?php echo e17($item['perms']); ?></td>
                <td class="ac">
                    <div class="row-acts">
                        <?php if (!$item['dir'] && view17($item['name'], $CFG)): ?>
                        <a href="?path=<?php echo urlencode($cwd); ?>&view=<?php echo urlencode($item['name']); ?>" class="ra ra-view">
                            View
                        </a>
                        <?php endif; ?>
                        <?php if (!$item['dir']): ?>
                        <a href="?path=<?php echo urlencode($cwd); ?>&download=<?php echo urlencode($item['name']); ?>" class="ra ra-dl">
                            DL
                        </a>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</main>
</div>

</body>
</html>
