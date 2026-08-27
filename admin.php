<?php
session_start();
const PASSWORD_FILE = __DIR__ . '/password.hash';
const ADMIN_FILE = __DIR__ . '/admin.hash';
if (!file_exists(PASSWORD_FILE)) file_put_contents(PASSWORD_FILE,password_hash('nurin',PASSWORD_DEFAULT),LOCK_EX);
if (!file_exists(ADMIN_FILE)) file_put_contents(ADMIN_FILE,password_hash('admin1234',PASSWORD_DEFAULT),LOCK_EX);

if (isset($_GET['logout'])) { unset($_SESSION['admin']); header('Location: admin.php'); exit; }

if (empty($_SESSION['admin'])) {
  $error='';
  if ($_SERVER['REQUEST_METHOD']==='POST') {
    $h=@file_get_contents(ADMIN_FILE);
    if ($h && password_verify((string)($_POST['admin_password']??''),trim($h))) {
      session_regenerate_id(true); $_SESSION['admin']=true; header('Location: admin.php'); exit;
    }
    $error='관리자 비밀번호가 올바르지 않습니다.';
  }
  ?>
  <!doctype html><html lang="ko"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>관리자 로그인</title>
  <style>body{margin:0;min-height:100vh;display:grid;place-items:center;background:#f4f7f7;font-family:system-ui,"Noto Sans KR",sans-serif}.box{width:min(400px,calc(100% - 40px));background:#fff;padding:28px;border:1px solid #dde6e3;border-radius:18px}input,button{width:100%;box-sizing:border-box;padding:13px;margin-top:10px;border-radius:10px;font-size:16px}input{border:1px solid #cdd8d4}button{border:0;background:#0f3b43;color:white;font-weight:700}.err{color:#b3261e;font-size:13px;margin-top:10px}</style></head><body><div class="box"><h2>관리자 설정</h2><p>관리자 비밀번호를 입력하세요.</p><form method="post"><input type="password" name="admin_password" required autofocus placeholder="관리자 비밀번호"><button>관리자 로그인</button></form><?php if($error):?><div class="err"><?=$error?></div><?php endif;?></div></body></html><?php exit; } ?>

<?php
$msg='';
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['new_password'])) {
  $p=(string)$_POST['new_password'];
  if (strlen($p)<4) $msg='접속 비밀번호는 4자 이상이어야 합니다.';
  else { file_put_contents(PASSWORD_FILE,password_hash($p,PASSWORD_DEFAULT),LOCK_EX); $msg='접속 비밀번호가 변경되었습니다.'; }
}
?>
<!doctype html><html lang="ko"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>접속 비밀번호 변경</title>
<style>body{margin:0;min-height:100vh;display:grid;place-items:center;background:#f4f7f7;font-family:system-ui,"Noto Sans KR",sans-serif}.box{width:min(440px,calc(100% - 40px));background:#fff;padding:28px;border:1px solid #dde6e3;border-radius:18px}input,button{width:100%;box-sizing:border-box;padding:13px;margin-top:10px;border-radius:10px;font-size:16px}input{border:1px solid #cdd8d4}button{border:0;background:#0f3b43;color:white;font-weight:700}.msg{margin-top:12px;color:#2f7a3a}</style></head><body><div class="box"><h2>접속 비밀번호 변경</h2><p>일반 사용자가 가이드에 접속할 때 사용하는 비밀번호입니다.</p><form method="post"><input type="password" name="new_password" minlength="4" required placeholder="새 접속 비밀번호"><button>변경하기</button></form><?php if($msg):?><div class="msg"><?=htmlspecialchars($msg,ENT_QUOTES,'UTF-8')?></div><?php endif;?><p><a href="index.php">가이드로 이동</a>　<a href="admin.php?logout=1">관리자 로그아웃</a></p></div></body></html>
