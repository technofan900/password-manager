<?php
require base_path("views/partials/header.php");
require base_path("views/partials/nav.php");
?>
<div class="container">
    <div class="folder">
        <details class="dropdown">
            <summary role="button" class="folders-toggle">
                Folders
            </summary>
            <ul>
                <li>
                    <a href="/passwords" <?= empty($currentFolder) ? 'aria-current="true"' : '' //what dis mean??>>All</a> 
                </li>
                <?php foreach ($folders as $folder) : ?>
                    <li>
                        <a href="/passwords?folder_id=<?= $folder['id'] ?>" <?= ($currentFolder && (int)$currentFolder === (int)$folder['id']) ? 'aria-current="true"' : '' //and dis??>>
                            <?= htmlspecialchars($folder['folder_name']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </details>
        <a role="button" class="fButton" href="/folders">Create folder</a>
    </div>
    <div>
        <?php
        $selectedFolderName = 'All';
        if (!empty($currentFolder)) {
            if (is_array($currentFolder) && isset($currentFolder['folder_name'])) {
                $selectedFolderName = $currentFolder['folder_name'];
            } elseif (!empty($folders) && is_array($folders)) {
                foreach ($folders as $folder) {
                    if ((int)$folder['id'] === (int)$currentFolder) {
                        $selectedFolderName = $folder['folder_name'];
                        break;
                    }
                }
            }
        }
        ?>
        <h2><?= htmlspecialchars($selectedFolderName) ?> - Saved passwords:</h2>
            <div>
                <table class="striped">
                    <thead>
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Login</th>
                            <th scope="col">Folder</th>                                
                        </tr>
                    </thead>
                    <?php foreach($notes as $note) : ?>
                    <tbody>
                        <tr>
                            <th scope="row">
                                <a href="/password?id=<?=$note['id'] ?>">
                                <?= htmlspecialchars($note['name']) ?>
                                </a>
                            </th>
                            <td>
                                <?= htmlspecialchars($note['login_data']) ?>
                            </td>
                            <td>
                                <?php
                                $folderName = 'All';
                                if (!empty($note['folder_id'])) {
                                    foreach ($folders as $folder) {
                                        if ((int)$folder['id'] === (int)$note['folder_id']) {
                                            $folderName = $folder['folder_name'];
                                            break;
                                        }
                                    }
                                }
                                echo htmlspecialchars($folderName);
                                ?>
                            </td>
                        </tr>
                    </tbody>
                    <?php endforeach; ?>
                </table>
            </div>
        <a role="button" href="/passwords/create">Create</a>
    </div>
</div>


<?php
require base_path("views/partials/footer.php");
?>