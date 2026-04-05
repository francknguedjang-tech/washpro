const fs = require('fs');
const path = require('path');

// 1. Copy files
const dirsToCopy = [
    { src: 'node_modules/bootstrap/dist', dest: 'public/vendor/bootstrap' },
    { src: 'node_modules/bootstrap-icons/font', dest: 'public/vendor/bootstrap-icons' },
    { src: 'node_modules/chart.js/dist', dest: 'public/vendor/chartjs' },
    { src: 'node_modules/sweetalert2/dist', dest: 'public/vendor/sweetalert2' },
    { src: 'node_modules/animate.css', dest: 'public/vendor/animate' },
    { src: 'node_modules/@fortawesome/fontawesome-free', dest: 'public/vendor/fontawesome' },
    { src: 'node_modules/@fontsource/inter', dest: 'public/vendor/font-inter' },
    { src: 'node_modules/@fontsource/poppins', dest: 'public/vendor/font-poppins' },
    { src: 'node_modules/@fontsource/plus-jakarta-sans', dest: 'public/vendor/font-plus-jakarta-sans' },
];

dirsToCopy.forEach(dir => {
    if (fs.existsSync(dir.src)) {
        fs.cpSync(dir.src, dir.dest, { recursive: true, force: true });
    }
});

console.log("Files copied successfully");

// 2. Replacements in blade files
function getAllFiles(dirPath, arrayOfFiles) {
  const files = fs.readdirSync(dirPath);
  arrayOfFiles = arrayOfFiles || [];
  files.forEach(function(file) {
    if (fs.statSync(dirPath + "/" + file).isDirectory()) {
      arrayOfFiles = getAllFiles(dirPath + "/" + file, arrayOfFiles);
    } else {
      if (file.endsWith('.blade.php')) {
          arrayOfFiles.push(path.join(dirPath, "/", file));
      }
    }
  });
  return arrayOfFiles;
}

const viewFiles = getAllFiles('./resources/views');

const replacements = [
    { regex: /https:\/\/cdn\.jsdelivr\.net\/npm\/bootstrap@5\.\d+\.\d+\/dist\/css\/bootstrap\.min\.css/g, replacement: "{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" },
    { regex: /https:\/\/cdn\.jsdelivr\.net\/npm\/bootstrap@5\.\d+\.\d+\/dist\/js\/bootstrap\.bundle\.min\.js/g, replacement: "{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}" },
    { regex: /https:\/\/cdn\.jsdelivr\.net\/npm\/bootstrap-icons@\d+\.\d+\.\d+\/font\/bootstrap-icons\.css/g, replacement: "{{ asset('vendor/bootstrap-icons/bootstrap-icons.css') }}" },
    { regex: /https:\/\/cdn\.jsdelivr\.net\/npm\/bootstrap-icons@\d+\.\d+\.\d+\/font\/bootstrap-icons\.min\.css/g, replacement: "{{ asset('vendor/bootstrap-icons/bootstrap-icons.css') }}" },
    { regex: /https:\/\/cdn\.jsdelivr\.net\/npm\/chart\.js/g, replacement: "{{ asset('vendor/chartjs/chart.umd.js') }}" },
    { regex: /https:\/\/cdn\.jsdelivr\.net\/npm\/sweetalert2@11/g, replacement: "{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}" },
    { regex: /https:\/\/cdnjs\.cloudflare\.com\/ajax\/libs\/animate\.css\/\d+\.\d+\.\d+\/animate\.min\.css/g, replacement: "{{ asset('vendor/animate/animate.min.css') }}" },
    { regex: /https:\/\/cdnjs\.cloudflare\.com\/ajax\/libs\/font-awesome\/\d+\.\d+\.\d+\/css\/all\.min\.css/g, replacement: "{{ asset('vendor/fontawesome/css/all.min.css') }}" },
    // Google fonts
    { regex: /https:\/\/fonts\.googleapis\.com\/css2\?family=Inter:(?:wght@)?[0-9;&]*display=swap/g, replacement: "{{ asset('vendor/font-inter/index.css') }}" },
    { regex: /https:\/\/fonts\.googleapis\.com\/css2\?family=Poppins:(?:wght@)?[0-9;&]*display=swap/g, replacement: "{{ asset('vendor/font-poppins/index.css') }}" },
    { regex: /https:\/\/fonts\.googleapis\.com\/css2\?family=Plus\+Jakarta\+Sans:(?:wght@)?[0-9;&]*display=swap/g, replacement: "{{ asset('vendor/font-plus-jakarta-sans/index.css') }}" },
    { regex: /https:\/\/fonts\.googleapis\.com\/css2\?family=Plus\+Jakarta\+Sans:wght@300;400;500;600;700;800&display=swap/g, replacement: "{{ asset('vendor/font-plus-jakarta-sans/index.css') }}" },
    // Some fixed strings that regex might miss depending on encoded ampersands
    { regex: /https:\/\/fonts\.googleapis\.com\/css2\?family=Inter:wght@300;400;500;600;700;800&display=swap/g, replacement: "{{ asset('vendor/font-inter/index.css') }}" },
    { regex: /https:\/\/fonts\.googleapis\.com\/css2\?family=Poppins:wght@300;400;500;600;700&display=swap/g, replacement: "{{ asset('vendor/font-poppins/index.css') }}" },
    { regex: /https:\/\/fonts\.googleapis\.com\/css2\?family=Poppins:wght@300;400;500;600&display=swap/g, replacement: "{{ asset('vendor/font-poppins/index.css') }}" },
    { regex: /https:\/\/cdn\.jsdelivr\.net\/npm\/bootstrap@5\.3\.0\/dist\/css\/bootstrap\.min\.css/g, replacement: "{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" },
    { regex: /https:\/\/cdn\.jsdelivr\.net\/npm\/bootstrap@5\.3\.0\/dist\/js\/bootstrap\.bundle\.min\.js/g, replacement: "{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}" }
];

viewFiles.forEach(file => {
    let content = fs.readFileSync(file, 'utf8');
    let original = content;
    replacements.forEach(r => {
        content = content.replace(r.regex, r.replacement);
    });
    // also fallback for hardcoded specific strings from the grep search earlier
    content = content.replace('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap', "{{ asset('vendor/font-plus-jakarta-sans/index.css') }}");
    content = content.replace('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap', "{{ asset('vendor/font-poppins/index.css') }}");
    content = content.replace('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap', "{{ asset('vendor/font-poppins/index.css') }}");
    content = content.replace('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap', "{{ asset('vendor/font-inter/index.css') }}");
    content = content.replace('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap', "{{ asset('vendor/font-inter/index.css') }}");
    
    if (content !== original) {
        fs.writeFileSync(file, content, 'utf8');
        console.log('Updated: ' + file);
    }
});
