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
<?php try {
    var_dump($_GET);
    $pdo = connect();
    $pdo->query('SET NAMES UTF8');
    $search_word = $_GET['word'];
    $keyword = '%' . $search_word . '%';
    var_dump($keyword); // $start = filter_input(INPUT_GET, 'start_date'); // $end = filter_input(INPUT_GET, 'end_date');
    $sql = 'SELECT * FROM pages WHERE content LIKE :keyword'; // $data[] = $start; // $data[] = $end;
    $statement->bindValue(':keyword', $keyword, PDO::PARAM_STR);
    $statement = $pdo->prepare($sql);
    $statement->execute();
    $memos = $statement->fetchAll(PDO::FETCH_ASSOC);
    var_dump($memos);
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
    <form action="" method="GET">
      <input type="text" name="word" placeholder="Search..."/>
      <input type="text" name="start_date" placeholder="2022-05-04">〜<input type="text" name="end_date" placeholder="2022-05-06">
    </div>
    <input type="submit" value="検索"/>
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