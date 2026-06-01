<!DOCTYPE html>
<html lang="lv" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Classless CSS Mājaslapa</title>

  <script>
  (function(){
      try {
      const stored = localStorage.getItem('theme');
      if (stored === 'dark') {
        document.documentElement.setAttribute('data-theme', 'dark');
      } else {
        document.documentElement.setAttribute('data-theme', 'light');
      }
    } catch(e) { }

    window.__setTheme = function(isDark){
      try {
        if (isDark) {
          document.documentElement.setAttribute('data-theme', 'dark');
          localStorage.setItem('theme', 'dark');
        } else {
          document.documentElement.setAttribute('data-theme', 'light');
          localStorage.setItem('theme', 'light');
        }
      } catch(e) {}
    };

    document.addEventListener('DOMContentLoaded', function(){
      try {
        var cb = document.getElementById('theme-switch');
        if (!cb) return;
        cb.checked = document.documentElement.getAttribute('data-theme') === 'dark';
        cb.addEventListener('change', function(){ __setTheme(this.checked); });
      } catch(e) {}
    });
  })();
  </script>

  <!-- Pico.css (classless) and other-->
  <link rel="stylesheet" href="https://unpkg.com/@picocss/pico@latest/css/pico.min.css">
  <link rel="stylesheet" href="/style.css">
</head>
<body>
