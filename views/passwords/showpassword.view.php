<?php
require base_path("views/partials/header.php");
require base_path("views/partials/nav.php");
?>

<div class="container">
    <div>
        <h1><?= $note['name'] ?></h1>
        <label for="login_data">Login</label>
        <div style="display:flex;gap:8px;align-items:center">
            <input id="login_data" type="text" name="login_data" value="<?= $note['login_data'] ?>" aria-label="Read-only login" readonly>
            <button type="button" class="copy-btn" data-copy-target="login_data">Copy</button>
        </div>
        <label for="password">Password</label>
        <div style="display:flex;gap:8px;align-items:center">
            <input id="password" type="text" name="password" value="<?= $note['password'] ?>" aria-label="Read-only password" readonly>
            <button type="button" class="copy-btn" data-copy-target="password">Copy</button>
        </div>
    </div>
    <div class="attachment">
        <?php if (!empty($note['attachment'])):
            $attachmentUrl = '/attachment?id=' . urlencode($note['id']);
            $previewUrl = $attachmentUrl . '&inline=1';
            $basename = basename($note['attachment']);
            $storagePath = base_path('storage/uploads/' . $basename);
            $ext = strtolower(pathinfo($basename, PATHINFO_EXTENSION));
        ?>
            <label for="">Attachment</label>
            <?php if ($ext === 'png') : ?>
                <img src="<?= $previewUrl ?>" alt="attachment" style="max-width:400px;max-height:400px">
            <?php elseif ($ext === 'pdf') : ?>
                <p><a href="<?= $attachmentUrl ?>" target="_blank">Open PDF in new tab</a></p>
                <iframe src="<?= $previewUrl ?>" style="width:100%;height:600px;border:0" title="PDF preview"></iframe>
            <?php elseif ($ext === 'txt') : ?>
                <?php if (file_exists($storagePath)) :
                    $raw = decrypt_file_to_string($storagePath);
                    if ($raw === false) {
                        echo '<p>Unable to read attachment.</p>';
                    } else {
                        $text = htmlspecialchars($raw);
                        ?><pre style="white-space:pre-wrap;word-break:break-word;"><?= $text ?></pre><?php
                    }
                else: ?>
                    <p>Text file missing.</p>
                <?php endif; ?>
            <?php else: ?>
                <p><a href="<?= $attachmentUrl ?>" target="_blank">View attachment</a></p>
            <?php endif; ?>

            <p><a href="<?= $attachmentUrl ?>&download=1">Download</a></p>
        <?php else: ?>
            <p>No attachment.</p>
        <?php endif; ?>
    </div>
    <div>
        <?php foreach($folders as $folder) : ?>
            <div class="form-group">
                <label for="folder_name">Folder</label>
                <input type="text" name="folder_name" id="folder_name" value="<?= htmlspecialchars($folder['folder_name']) ?>" readonly>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="buttons">
        <p>
            <a href="/password/edit?id=<?= $note['id'] ?>">Edit</a>
            <a href="/password/popup?id=<?= $note['id'] ?>">Delete</a>

            <a href="/passwords">Back</a>
        </p>
    </div>
</div>

<?php
require base_path("views/partials/footer.php");
?>

<script>
document.addEventListener('click', function(e){
    if (!e.target.classList) return;
    if (e.target.classList.contains('copy-btn')){
        var targetId = e.target.getAttribute('data-copy-target');
        var input = document.getElementById(targetId);
        if (!input) return;
        var value = input.value || '';
        if (navigator.clipboard && navigator.clipboard.writeText){
            navigator.clipboard.writeText(value).then(function(){
                showTemporary(e.target, 'Copied!');
            }).catch(function(){
                fallbackCopy(value, e.target);
            });
        } else {
            fallbackCopy(value, e.target);
        }
    }
});

function fallbackCopy(text, btn){
    var ta = document.createElement('textarea');
    ta.value = text;
    ta.style.position = 'fixed';
    ta.style.left = '-9999px';
    document.body.appendChild(ta);
    ta.select();
    try{
        document.execCommand('copy');
        showTemporary(btn, 'Copied!');
    }catch(err){
        showTemporary(btn, 'Unable');
    }
    document.body.removeChild(ta);
}

function showTemporary(btn, text){
    var original = btn.textContent;
    btn.textContent = text;
    setTimeout(function(){ btn.textContent = original; }, 1500);
}
</script>