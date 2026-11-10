<style>
    body { font-family: sans-serif; color: #333; line-height: 1.5; font-size: 11px; }
    .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
    .header h1 { margin: 0; font-size: 20px; color: #0064E0; }
    .header p { margin: 5px 0 0 0; color: #666; font-size: 12px; }
    
    .section-title { font-size: 14px; font-weight: bold; margin: 20px 0 10px 0; border-bottom: 1px solid #ccc; padding-bottom: 5px; color: #0064E0; }
    
    table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    table.data-table th, table.data-table td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; font-size: 10px; }
    table.data-table th { background-color: #f1f5f9; font-weight: bold; }
    
    .text-right { text-align: right; }
    .text-center { text-align: center; }
    
    .badge { font-weight: bold; padding: 2px 4px; display: inline-block; }
    
    .metric-grid { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
    .metric-grid td { padding: 10px; border: 1px solid #ddd; text-align: center; background: #f8f9fa; }
    .metric-title { font-size: 10px; text-transform: uppercase; color: #666; font-weight: bold; }
    .metric-value { font-size: 16px; font-weight: bold; color: #111; margin-top: 5px; }
</style>

@if(isset($isPrint) && $isPrint)
<style>
    @media print {
        .no-print { display: none !important; }
    }
    .back-nav {
        padding: 12px 20px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .back-btn {
        padding: 8px 16px;
        background: #0064E0;
        color: #ffffff;
        text-decoration: none;
        border-radius: 6px;
        font-weight: bold;
        font-size: 12px;
        border: none;
        cursor: pointer;
    }
    .back-btn:hover {
        background: #0052b8;
    }
</style>
<div class="no-print back-nav">
    <span style="font-size: 13px; font-weight: bold; color: #334155;">Pratinjau Cetak (Print Preview)</span>
    <button onclick="goBackToList()" class="back-btn">&larr; Kembali ke Daftar</button>
</div>
<script>
    function goBackToList() {
        const url = new URL(window.location.href);
        url.searchParams.delete('print');
        window.location.replace(url.toString());
    }

    window.addEventListener('afterprint', () => {
        goBackToList();
    });

    window.addEventListener('DOMContentLoaded', () => {
        setTimeout(() => {
            window.print();
            setTimeout(() => {
                goBackToList();
            }, 500);
        }, 300);
    });
</script>
@endif
