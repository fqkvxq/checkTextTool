new ClipboardJS(".clipboardButton");
window.addEventListener(
    "beforeunload",
    function (e) {
        e.returnValue = "離脱しますか？";
    },
    false
);
var currentArrayNumber = 0; // 現在選択している配列番号(初期は0)
var splitedText = ""; // 配列
var maxArrayCount = 1; // 配列数(初期は1)
var currentPercent = 0;
var bar = "";
progressBar(currentPercent);

function toast() {
    iziToast.show({
        title: "コピー完了",
        message: "クリップボードにコピーしました！",
        color: "green",
        position: "topRight",
        timeout: 3000
    });
}

function progressBar(currentPercent) {
    // progressbar.js@1.0.0 version is used
    // Docs: http://progressbarjs.readthedocs.org/en/1.0.0/

    bar = new ProgressBar.Line(showProgressBar, {
        strokeWidth: 4,
        easing: "easeInOut",
        duration: 1400,
        color: "#FFEA82",
        trailColor: "#eee",
        trailWidth: 1,
        svgStyle: {
            width: "100%",
            height: "100%"
        },
        text: {
            style: {
                // Text color.
                // Default: same as stroke color (options.color)
                color: "#999",
                position: "absolute",
                right: "0",
                top: "30px",
                padding: 0,
                margin: 0,
                transform: null
            },
            autoStyleContainer: false
        },
        from: {
            color: "#FFEA82"
        },
        to: {
            color: "#ED6A5A"
        },
        step: (state, bar) => {
            bar.setText(Math.round(bar.value() * 100) + " %");
        }
    });
    bar.animate(currentPercent); // Number from 0.0 to 1.0
}

// 編集
function startEdit() {
    // OKの処理を記載
    var inputText = getInputArea();
    var removedTagText = removeHtmlTag(inputText);
    // 文字を分割する
    splitedText = removedTagText.split(/(?<=。|？|！|<\/h[1-6]>)/g);
    console.log(splitedText[0]);
    showArrayText(splitedText);
    // 進捗率を計算
    currentPercent = currentArrayNumber / maxArrayCount;
    bar.animate(currentPercent);
    // 進捗率を計算
    console.log("currentArrayNumber=" + currentArrayNumber);
    console.log("maxArrayCount=" + maxArrayCount);
    console.log("現時点の進捗=" + currentPercent);
    document.getElementById('originalText').textContent = inputText;
    document.getElementById('source').textContent = inputText;
    document.getElementById('currentParagraph').textContent = currentArrayNumber+1;
    document.getElementById('allOfParagraph').textContent = maxArrayCount;
    getCurrentMojisuu();
}

function haneiEdit() {
    // OKの処理を記載
    var inputText = getInputArea();
    var removedTagText = removeHtmlTag(inputText);
    // 文字を分割する
    splitedText = removedTagText.split(/(?<=。|？|！|<\/h[1-6]>)/g);
    console.log(splitedText[0]);
    showArrayText(splitedText);
    // 進捗率を計算
    currentPercent = currentArrayNumber / maxArrayCount;
    bar.animate(currentPercent);
    // 進捗率を計算
    console.log("currentArrayNumber=" + currentArrayNumber);
    console.log("maxArrayCount=" + maxArrayCount);
    console.log("現時点の進捗=" + currentPercent);
    getCurrentMojisuu();
}

// 編集終了時の処理
function endEdit() {
    // 配列の中身を結合する
    var editedText = splitedText.join("");
    console.log(editedText);
    // テキストエリアに表示する
    document.getElementById("editedTextArea").value = editedText;
    document.getElementById("changedText").textContent = editedText;
    document.getElementById("change").textContent = editedText;
    var originalText = document.getElementById('originalText').textContent;
    document.getElementById("copyTextArea").value = "修正前：" + originalText + "\n\n" + "修正後：" + editedText;
}

//配列をテキストエリアに表示(ボタンを押したとき)
function showArrayText(splitedText) {
    // 表示中の配列番号を取得(初期値0)
    currentArrayNumber = 0;
    document.getElementById("showPrevTextArea").value = "";
    document.getElementById("showTextArea").value =
        splitedText[currentArrayNumber];
    document.getElementById("showNextTextArea").value =
        splitedText[currentArrayNumber + 1];
    // 配列の要素数を取得
    maxArrayCount = splitedText.length;
    endEdit();
}

// 文字の編集を行う
function editText(splitedText) {
    splitedText[currentArrayNumber] = document.getElementById("showTextArea").value;
    document.getElementById("showTextArea").value = splitedText[currentArrayNumber];
    getCurrentMojisuu();
    endEdit();
}

// 次へを押したときの処理
function goNextSentence(splitedText) {
    console.log(splitedText);
    if (currentArrayNumber < maxArrayCount - 1) {
        currentArrayNumber += 1;
        // 進捗率を計算
        document.getElementById("showPrevTextArea").value =
            splitedText[currentArrayNumber - 1];
        document.getElementById("showTextArea").value =
            splitedText[currentArrayNumber];
        document.getElementById("showNextTextArea").value =
            splitedText[currentArrayNumber + 1];
        document.getElementById('currentParagraph').textContent = currentArrayNumber+1;
        document.getElementById('allOfParagraph').textContent = maxArrayCount;
        console.log("currentArrayNumber=" + currentArrayNumber);
        console.log("maxArrayCount=" + maxArrayCount);
        currentPercent = currentArrayNumber / maxArrayCount;
        bar.animate(currentPercent);
    }
    console.log("現時点の進捗=" + currentPercent);
    getCurrentMojisuu();
    
}

function getCurrentMojisuu(){
    document.getElementById("currentMojisuu").textContent = splitedText[currentArrayNumber].length;
}

// 戻るを押したときの処理
function goPrevSentence() {
    console.log(splitedText);
    if (currentArrayNumber > 0) {
        currentArrayNumber -= 1;
        document.getElementById("showPrevTextArea").value =
            splitedText[currentArrayNumber - 1];
        document.getElementById("showTextArea").value =
            splitedText[currentArrayNumber];
        document.getElementById("showNextTextArea").value =
            splitedText[currentArrayNumber + 1];
        document.getElementById('currentParagraph').textContent = currentArrayNumber+1;
        document.getElementById('allOfParagraph').textContent = maxArrayCount;
        // 進捗率を計算
        currentPercent = currentArrayNumber / maxArrayCount;
        bar.animate(currentPercent);
    }
    getCurrentMojisuu();
}

// 編集確認
function editCheck(text) {
    if (confirm(text)) {
        startEdit();
    }
}

// 作成完了確認
function syuturyokuCheck(text) {
    if (confirm(text)) {
        endEdit();
    }
}

// 入力のテキストを取得
function getInputArea() {
    const getText = document.getElementById("inputArea").value;
    return getText;
}

// タグ、空白削除
function removeHtmlTag(text) {
    var removedText = removeTag(text, ["h1", "h2", "h3", "h4", "h5", "h6"]);
    removedText = removedText.replace(/\s+/g, "");
    return removedText;
}

// 指定したタグ以外のタグをすべて削除
function removeTag(str, arrowTag) {
    // 配列形式の場合は'|'で結合
    if (
        Array.isArray ?
            Array.isArray(arrowTag) :
            Object.prototype.toString.call(arrowTag) === "[object Array]"
    ) {
        arrowTag = arrowTag.join("|");
    }

    // arrowTag が空の場合は全てのHTMLタグを除去する
    arrowTag = arrowTag ? arrowTag : "";

    // パターンを動的に生成
    var pattern = new RegExp(
        "(?!<\\/?(" +
        arrowTag +
        ")(>|\\s[^>]*>))<(\"[^\"]*\"|\\'[^\\']*\\'|[^\\'\">])*>",
        "gim"
    );

    return str.replace(pattern, "");
}

function syuuseiHanei() {
    var editedText = document.getElementById("editedTextArea").value;
    document.getElementById("inputArea").value = editedText;
    haneiEdit();
}