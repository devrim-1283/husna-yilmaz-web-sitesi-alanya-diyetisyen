#!/bin/bash
# Tüm araç sayfalarına analytics tracking ekle

# Araç sayfaları ve tipleri
declare -A tools
tools["vki.php"]="tool_vki"
tools["bmh.php"]="tool_bmh"
tools["bel-kalca-orani.php"]="tool_bel_kalca"
tools["gunluk-kalori.php"]="tool_kalori"
tools["gunluk-karbonhidrat.php"]="tool_karbonhidrat"
tools["gunluk-makro.php"]="tool_makro"
tools["gunluk-protein.php"]="tool_protein"
tools["gunluk-su.php"]="tool_su"
tools["gunluk-yag.php"]="tool_yag"
tools["ideal-kilo.php"]="tool_ideal_kilo"
tools["vucut-yag-orani.php"]="tool_vucut_yag"

echo "Analytics tracking ekleniyor..."

for file in "${!tools[@]}"; do
    type="${tools[$file]}"
    echo "Processing: $file => $type"
    
    # Dosya mevcutsa
    if [ -f "$file" ]; then
        # analytics.php require satırını ekle (eğer yoksa)
        if ! grep -q "analytics.php" "$file"; then
            sed -i "2a require_once __DIR__ . '/includes/analytics.php';" "$file"
        fi
        
        # trackPageView çağrısını ekle (eğer yoksa)
        if ! grep -q "trackPageView" "$file"; then
            sed -i "/require_once.*analytics.php/a \\ntrackPageView('$type', \$_SERVER['REQUEST_URI']);" "$file"
        fi
        
        echo "✓ $file updated"
    else
        echo "✗ $file not found"
    fi
done

echo "Done!"

