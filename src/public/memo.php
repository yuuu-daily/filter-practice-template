<?php
declare(strict_types=1);
function connect(): PDO
{
    $dsn = 'mysql:host=mysql; dbname=tq_filter; charset=utf8';
    $dbUserName = 'root';
    $dbPassword = 'password';
    $pdo = new PDO($dsn, $dbUserName, $dbPassword);

    return $pdo;
}
?>
<!-- デフォルト設定 （全取得）-->
<?php
$pdo = connect();
$pdo->query('SET NAMES UTF8');
$sql = 'SELECT * FROM pages';
$statement = $pdo->prepare($sql);
$statement->bindValue(':title', $title, PDO::PARAM_STR);
$statement->bindValue(':content', $content, PDO::PARAM_STR);
$statement->execute();
$pages = $statement->fetchAll(PDO::FETCH_ASSOC);
?>
<!-- 検索されたものの取得 -->
<?php try {
    var_dump($_GET);
    $pdo = connect();
    $pdo->query('SET NAMES UTF8');
    $search_word = $_POST['word'];
    $sql =
        "SELECT * FROM pages WHERE content LIKE '%" .
        $search_word .
        "%' OR title LIKE '%" .
        $search_word .
        "%' ORDER BY created_at";
    if ($_GET['order'] === 'desc') {
        //降順に並び替えるSQL文に変更
        $sql = $sql . ' DESC';
    } elseif ($_GET['order'] === 'asc') {
        //昇順に並び替えるSQL文に変更
        $sql = $sql . ' ASC';
    }
    $statement = $pdo->prepare($sql);
    $statement->execute();
    $memos = $statement->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo 'DB接続エラー' . $e->getMessage();
} ?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>top画面</title>
</head>

<body>
  <div>
    <div>
    <form action="" method="POST">
      <input type="text" name="word" placeholder="Search..."/><input type="submit" value="検索"/>
    </form>
      <form action="memo.php" method="GET">
        <div>
          <label>
            <input type="radio" name="order" value="desc" class=""
            <?php if (!isset($_GET['order']) || $_GET['order'] == 'desc') {
                echo 'checked';
            } ?>>
            <span>新着順</span>
          </label>
          <label>
            <input type="radio" name="order" value="asc" class=""
            <?php if (isset($_GET['order']) && $_GET['order'] != 'desc') {
                echo 'checked';
            } ?>>
            <span>古い順</span>
          </label>
        </div>
        <input type="submit" value="並び替え">
      </form>
    </div>
    
    <div>
      <table border="1">
        <tr>
          <th>タイトル</th>
          <th>内容</th>
          <th>作成日時</th>
        </tr>
        <?php if (empty($memos)): ?>
        <?php foreach ($pages as $page): ?>
          <tr>
            <td><?php echo $page['title']; ?></td>
            <td><?php echo $page['content']; ?></td>
            <td><?php echo $page['created_at']; ?></td>
          </tr>
        <?php endforeach; ?>
        <?php endif; ?>
        <?php if ($memos): ?>
        <?php foreach ($memos as $memo): ?>
          <tr>
            <td><?php echo $memo['title']; ?></td>
            <td><?php echo $memo['content']; ?></td>
            <td><?php echo $memo['created_at']; ?></td>
          </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </table>
    </div>
  </div>
</body>

</html>