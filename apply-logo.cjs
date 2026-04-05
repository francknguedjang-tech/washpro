const fs = require('fs');
const path = require('path');

// 1. Create the SVG logo
const svgLogo = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 220 60" width="220" height="60">
  <!-- Droplet -->
  <path d="M30 6C30 6 7 28 7 42C7 54.7 17.3 65 30 65C42.7 65 53 54.7 53 42C53 28 30 6 30 6Z" fill="#3b82f6" />
  <!-- Shine/Reflection line -->
  <line x1="16" y1="34" x2="23" y2="21" stroke="#ffffff" stroke-width="4" stroke-linecap="round" />
  <!-- Text -->
  <text x="65" y="45" font-family="'Plus Jakarta Sans', 'Inter', sans-serif" font-size="34" font-weight="800" fill="#3b82f6" letter-spacing="-1.5">washpro</text>
</svg>`;

const logoDir = path.join(__dirname, 'public', 'img');
if (!fs.existsSync(logoDir)) {
    fs.mkdirSync(logoDir, { recursive: true });
}
fs.writeFileSync(path.join(logoDir, 'logo.svg'), svgLogo);

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

const sidebarRegex = /--sidebar-width:\s*2[0-9]{2}px;/g;
const logoContainerRegex = /<div class="logo-icon">[\s\S]*?<\/div>\s*<h5 class="brand-name">WashPro<\/h5>/g;
const brandLogoRegex1 = /<span class="brand-logo">WashPro<\/span>/g;
const brandLogoRegex2 = /<h1 class="display-5 fw-bold mb-3">WashPro<\/h1>/g;
const simpleBrandText = /<span class="fw-bold text-primary">WashPro<\/span>/g;
const brandLogoMobile = /<span class="brand-logo">WashPro<\/span>\s*<p class="text-muted">Votre gestionnaire de confiance<\/p>/g;

const genericLogoReplacement = `<img src="{{ asset('img/logo.svg') }}" alt="WashPro Logo" style="height: 48px; width: auto;">`;
const brandLogoMobileRepl = `<img src="{{ asset('img/logo.svg') }}" alt="WashPro Logo" style="height: 55px; width: auto;" class="mb-2"><p class="text-muted small">Votre gestionnaire de confiance</p>`;
const h1Replacement = `<img src="{{ asset('img/logo.svg') }}" alt="WashPro Logo" style="height: 60px; width: auto;" class="mb-4">`;
const adminTopbarRepl = `<img src="{{ asset('img/logo.svg') }}" alt="WashPro Logo" style="height: 32px; width: auto;">`;

viewFiles.forEach(file => {
    let content = fs.readFileSync(file, 'utf8');
    let original = content;

    // Sidebar width
    content = content.replace(sidebarRegex, '--sidebar-width: 280px;');

    // Sidebar logo block (admin style)
    content = content.replace(logoContainerRegex, genericLogoReplacement);

    // Login page / Auth pages mobile
    content = content.replace(brandLogoMobile, brandLogoMobileRepl);

    // Other brand logo spans
    content = content.replace(brandLogoRegex1, genericLogoReplacement);
    
    // Large H1 in left panel of Auth
    content = content.replace(brandLogoRegex2, h1Replacement);

    // Mobile Topbar (admin, technicien, etc)
    content = content.replace(simpleBrandText, adminTopbarRepl);

    if (content !== original) {
        fs.writeFileSync(file, content, 'utf8');
        console.log('Updated: ' + file);
    }
});
