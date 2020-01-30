<?php
if (array_key_exists('source', $_POST) && array_key_exists('change', $_POST)) {
  include('./textDiff.php');
  $diff = new TextDiff($_POST['source'], $_POST['change']);
  $html = $diff->getHtml();
  $html['data'] = var_export($diff->getData(), true);
  echo json_encode($html);
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <!-- meta -->
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>1文ずつ確認して校正するツール.js</title>
  <!-- reset -->
  <link　rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.css" />
  <!-- bootstrap -->
  <link rel="stylesheet" href="./bootstrap-grid.min.css" />
  <link rel="stylesheet" href="./bootstrap.min.css" />
  <link rel="stylesheet" href="./bootstrap-reboot.min.css" />
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
  <!-- html2canvas -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.js"></script>
  <!-- toast -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css" />
  <!-- swiper -->
  <link rel="stylesheet" href="https://unpkg.com/swiper/css/swiper.css" />
  <link rel="stylesheet" href="https://unpkg.com/swiper/css/swiper.min.css" />
  <script src="https://unpkg.com/swiper/js/swiper.js"></script>
  <script src="https://unpkg.com/swiper/js/swiper.min.js"></script>
  <!-- custom -->
  <link rel="stylesheet" href="./custom.css" />
  <link rel="stylesheet" href="./textDiff.css">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Sawarabi+Mincho" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css?family=M+PLUS+1p" rel="stylesheet" />
  <!-- progressbar -->
  <script src="https://rawcdn.githack.com/kimmobrunfeldt/progressbar.js/e0e1a8a67f83934d131dedca270fcc3b0e55d0c6/dist/progressbar.js"></script>
  <!-- clipboard.js -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.4/clipboard.min.js"></script>
  <title>1文ずつ確認して校正するツール.js</title>
</head>

<body>
  <!-- nav -->
  <div class="container-fluid navigation">
    <div class="row">
      <div class="col-12">
        <h1 class="text-center">1文ずつ確認して校正するツール.js</h1>
      </div>
    </div>
  </div>

  <!-- 元テキスト入力部分 -->
  <div class="container original-text">
    <div class="row">
      <div class="col-12 heading">
        <h2 class="text-center">
          ステップ①：下記にチェックしたいテキストをコピペしてください。
        </h2>
      </div>
    </div>
    <div class="row">
      <div class="col-12 px-0 inputarea">
        <textarea class="form-control" name="" id="inputArea" cols="30" rows="3" onclick="this.select()">この中に、チェックしたい文章を入力してください。</textarea>
      </div>
    </div>
  </div>

  <!-- ボタン部分 -->
  <div class="container button">
    <div class="row">
      <div class="col-12 button-individual" onclick='editCheck("編集を開始しますか？");'>
        <p class="text-center">ここを押して、編集スタート</p>
      </div>
    </div>
  </div>

  <!-- 前のテキストを表示 -->
  <div class="container display mt-0">
    <div class="row">
      <div class="col-12 px-0">
        <!-- ここにテキストボックスの文章を表示する -->
        <textarea class="form-control" name="" id="showPrevTextArea" cols="30" rows="3" readonly>前の文が表示されます。(ここでは編集できません。)</textarea>
      </div>
    </div>
  </div>

  <!-- テキスト表示 -->
  <div class="container display mt-0">
    <div class="row">
      <div class="col-12 px-0">
        <!-- ここにテキストボックスの文章を表示する -->
        <textarea class="form-control" name="" id="showTextArea" cols="30" rows="5" onInput="editText(splitedText)">現在選択されている文章がここに表示されます。ここで文章を編集してください。</textarea>
      </div>
    </div>
  </div>

  <!-- ボタン部分 -->
  <div class="container button">
    <div class="row">
      <div class="col-6 button-individual" onclick="goPrevSentence(splitedText)">
        <p class="text-center">(←)前の1文へ移動する</p>
      </div>
      <div class="col-6 button-individual" onclick="goNextSentence(splitedText)">
        <p class="text-center">次の1文へ移動する(→)</p>
      </div>
    </div>
  </div>

  <!-- 次のテキストを表示 -->
  <div class="container display mt-0">
    <div class="row">
      <div class="col-12 px-0">
        <!-- ここにテキストボックスの文章を表示する -->
        <textarea class="form-control" name="" id="showNextTextArea" cols="30" rows="3" readonly>次の文が表示されます。(ここでは編集できません。)</textarea>
      </div>
    </div>
  </div>

  <!-- progress bar -->
  <div class="container">
    <div class="row">
      <div class="col-12 px-0 my-1">
        <div id="showProgressBar"></div>
      </div>
    </div>
  </div>

  <!-- 完成テキスト部分 -->
  <div class="container original-text">
    <div class="row">
      <div class="col-12 heading mt-0">
        <h2 class="text-center">ステップ②：下記に完成されたテキストが表示されます。(編集内容はその場で反映されます。)</h2>
      </div>
    </div>
    <div class="row">
      <div class="col-12 px-0 inputarea">
        <textarea class="form-control" name="" id="editedTextArea" cols="30" rows="5" readonly>ここに完成されたテキストが表示されます。(ここでは編集できません。)</textarea>
      </div>
    </div>
  </div>

  <!-- ボタン部分 -->
  <div class="container button">
    <div class="row">
      <div class="col-3 button-individual" onclick="syuuseiHanei()">
        <p class="text-center">修正を編集に反映</p>
      </div>
      <div class="col-9 button-individual clipboardButton" data-clipboard-target="#editedTextArea" onclick="toast()">
        <p class="text-center">修正を完了してコピー</p>
      </div>
    </div>
  </div>

  <!-- 差分検知機能ここから -->
  <?php
  // <div class="container">
  //   <div class="row" id="diffTexts">
  //     <!-- 元の文章 -->
  //     <div class="col-6 diff-original">
  //       変更前：<span id="originalText">original</span><br /><br />
  //     </div>
  //     <!-- 変更した文章 -->
  //     <div class="col-6 diff-changed">
  //       変更後：<span id="changedText">original</span>
  //     </div>
  //     <!-- コピー用textarea -->
  //     <textarea class="form-control" style="height:1px;padding:0;border:none;outline:none" name="" id="copyTextArea" cols="30" rows="10" readonly></textarea>
  //   </div>
  //   <!-- コピーボタン -->
  //   <div class="row button-individual button clipboardButton" data-clipboard-target="#copyTextArea" onclick="toast()">
  //     <div class="col-12">
  //       <p class="text-white text-center">差分をコピー(未実装)</p>
  //     </div>
  //   </div>
  // </div>
  ?>
  <!-- 差分検知機能ここまで -->

  <!-- PHP差分機能ここから -->
  <div class="container diffCheckPhp">
    <div class="row">
      <div class="col-md-6 px-0"><textarea class="form-control" name="" id="source" cols="50" rows="5" readonly></textarea></div>
      <div class="col-md-6 px-0"><textarea class="form-control" name="" id="change" cols="50" rows="5" readonly></textarea></div>
    </div>
    <!-- 差分表示のボタン -->
    <div class="row">
      <div class="col-md-12 px-0 text-center">
        <button type="button" id="button" class="btn btn-lg btn-primary btn-block">差分表示！</button>
      </div>
    </div>
    <!-- 差分表示部分 -->
    <div id="showDiffPhp" class="row my-3 row-eq-height">
      <div id="out_source" class="col-md-6 px-0"></div>
      <div id="out_change" class="col-md-6 px-0"></div>
    </div>
    <div class="row button-individual button" onclick="showImg()">
      <div class="col-12">
        <p class="text-white text-center my-0">画像を生成</p>
      </div>
    </div>
    <div class="row createdbutton">
    <a id="imgLink" href="">
        <img class="img-fluid" id=createdImg src="" alt="">
    </a>
    </div>
  </div>
  <!-- PHP差分機能ここまで -->

  <!-- 仕様説明 -->
  <div class="container mt-3">
    <div class="row">
      <div class="col-12">
        <h2 class="text-center">このツールの用途</h2>
        <ul>
          <li>
            きれいな文章を作るために、完成された文章を1文ずつ集中して確認、校正できます。
          </li>
        </ul>
      </div>
    </div>
  </div>

  <!-- 仕様説明 -->
  <div class="container">
    <div class="row">
      <div class="col-12">
        <h2 class="text-center">仕様</h2>
        <ul>
          <li>Chromeでのみ動作確認済みです。(Safari未対応です)</li>
          <li>元のテキストを入力したら、まずタグ、改行を除去します。</li>
          <li>
            改行と句点(「。」「！」「？」)の部分で文章を分割し、配列に1つづつ格納します。
          </li>
          <li>配列の中身のテキストを1つずつ表示部分に表示させます。</li>
          <li>
            「戻る」「次へ」ボタンで表示させる文章を前後に変更できます。
          </li>
          <li>上下のtextareaは連動する</li>
        </ul>
      </div>
    </div>
  </div>
  <script src="./custom.js"></script>
  <script src="./textDiff.js"></script>
</body>

</html>