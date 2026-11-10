import json

notebook = {
 "cells": [
  {
   "cell_type": "markdown",
   "metadata": {},
   "source": [
    "# Analisis Data Produk & Penjualan POS\n",
    "\n",
    "Notebook ini melakukan proses Exploratory Data Analysis (EDA), Data Cleaning, Feature Engineering, K-Means Clustering, dan Simple Moving Average (SMA) untuk mengklasifikasikan pergerakan penjualan produk dan merekomendasikan jumlah restock."
   ]
  },
  {
   "cell_type": "code",
   "execution_count": None,
   "metadata": {},
   "outputs": [],
   "source": [
    "import pandas as pd\n",
    "import numpy as np\n",
    "import matplotlib.pyplot as plt\n",
    "import seaborn as sns\n",
    "from sklearn.cluster import KMeans\n",
    "from datetime import datetime, timedelta\n",
    "import warnings\n",
    "warnings.filterwarnings('ignore')"
   ]
  },
  {
   "cell_type": "markdown",
   "metadata": {},
   "source": [
    "## 1. Load Data\n",
    "Membaca data dari file `transaction_data.csv` dan `product_stock_data.csv`."
   ]
  },
  {
   "cell_type": "code",
   "execution_count": None,
   "metadata": {},
   "outputs": [],
   "source": [
    "# Load Datasets\n",
    "transactions_df = pd.read_csv('../datasets/transaction_data.csv')\n",
    "stock_df = pd.read_csv('../datasets/product_stock_data.csv')\n",
    "\n",
    "print(\"Transactions Data Shape:\", transactions_df.shape)\n",
    "print(\"Stock Data Shape:\", stock_df.shape)\n",
    "transactions_df.head()"
   ]
  },
  {
   "cell_type": "markdown",
   "metadata": {},
   "source": [
    "## 2. Data Cleaning\n",
    "Pembersihan data mencakup penanganan data kosong, duplikat, dan penyeragaman format."
   ]
  },
  {
   "cell_type": "code",
   "execution_count": None,
   "metadata": {},
   "outputs": [],
   "source": [
    "# Menghapus duplikat jika ada\n",
    "transactions_df.drop_duplicates(inplace=True)\n",
    "stock_df.drop_duplicates(inplace=True)\n",
    "\n",
    "# Konversi format tanggal\n",
    "transactions_df['date'] = pd.to_datetime(transactions_df['date'])\n",
    "\n",
    "# Membersihkan spasi pada kode produk\n",
    "transactions_df['product_code'] = transactions_df['product_code'].astype(str).str.strip()\n",
    "stock_df['product_code'] = stock_df['product_code'].astype(str).str.strip()\n",
    "\n",
    "print(\"Missing values in transactions:\\n\", transactions_df.isnull().sum())\n",
    "print(\"\\nMissing values in stock:\\n\", stock_df.isnull().sum())"
   ]
  },
  {
   "cell_type": "markdown",
   "metadata": {},
   "source": [
    "## 3. Exploratory Data Analysis (EDA)\n",
    "Menganalisis statistik, jumlah terjual, dan pola penjualan."
   ]
  },
  {
   "cell_type": "code",
   "execution_count": None,
   "metadata": {},
   "outputs": [],
   "source": [
    "# Produk Terlaris\n",
    "top_selling = transactions_df.groupby('product_name')['quantity_sold'].sum().sort_values(ascending=False).head(10)\n",
    "plt.figure(figsize=(10, 6))\n",
    "sns.barplot(x=top_selling.values, y=top_selling.index, palette='viridis')\n",
    "plt.title('Top 10 Produk Terlaris')\n",
    "plt.xlabel('Jumlah Terjual')\n",
    "plt.ylabel('Nama Produk')\n",
    "plt.show()\n",
    "\n",
    "# Tren Penjualan Harian\n",
    "sales_trend = transactions_df.groupby('date')['quantity_sold'].sum()\n",
    "plt.figure(figsize=(12, 5))\n",
    "sales_trend.plot(kind='line', color='blue', marker='o')\n",
    "plt.title('Tren Penjualan Harian')\n",
    "plt.xlabel('Tanggal')\n",
    "plt.ylabel('Total Terjual')\n",
    "plt.grid()\n",
    "plt.show()"
   ]
  },
  {
   "cell_type": "markdown",
   "metadata": {},
   "source": [
    "## 4. Feature Engineering\n",
    "Membuat fitur-fitur baru seperti total penjualan, frekuensi, periode jualan, rata-rata penjualan."
   ]
  },
  {
   "cell_type": "code",
   "execution_count": None,
   "metadata": {},
   "outputs": [],
   "source": [
    "# Agregasi transaksi per produk\n",
    "product_sales = transactions_df.groupby('product_code').agg(\n",
    "    total_quantity_sold=('quantity_sold', 'sum'),\n",
    "    transaction_frequency=('quantity_sold', 'count'),\n",
    "    first_sale_date=('date', 'min'),\n",
    "    last_sale_date=('date', 'max')\n",
    ").reset_index()\n",
    "\n",
    "# Hitung periode penjualan dan rata-rata per hari\n",
    "product_sales['sales_period_days'] = (product_sales['last_sale_date'] - product_sales['first_sale_date']).dt.days\n",
    "product_sales['sales_period_days'] = product_sales['sales_period_days'].apply(lambda x: x if x > 0 else 1) # Minimal 1 hari\n",
    "product_sales['avg_daily_sales'] = product_sales['total_quantity_sold'] / product_sales['sales_period_days']\n",
    "\n",
    "# Gabungkan dengan Data Stok. Pastikan produk tanpa penjualan (dead stock) tetap ada.\n",
    "merged_df = pd.merge(stock_df, product_sales, on='product_code', how='left')\n",
    "\n",
    "# Isi NaN untuk produk yang tidak pernah terjual\n",
    "merged_df['total_quantity_sold'] = merged_df['total_quantity_sold'].fillna(0)\n",
    "merged_df['transaction_frequency'] = merged_df['transaction_frequency'].fillna(0)\n",
    "merged_df['avg_daily_sales'] = merged_df['avg_daily_sales'].fillna(0)\n",
    "merged_df['current_stock'] = pd.to_numeric(merged_df['current_stock'], errors='coerce').fillna(0)\n",
    "\n",
    "merged_df.head()"
   ]
  },
  {
   "cell_type": "markdown",
   "metadata": {},
   "source": [
    "## 5. K-Means Clustering\n",
    "Mengelompokkan produk menjadi 3 kategori (Low, Medium, High Movement)."
   ]
  },
  {
   "cell_type": "code",
   "execution_count": None,
   "metadata": {},
   "outputs": [],
   "source": [
    "from sklearn.preprocessing import StandardScaler\n",
    "\n",
    "# Fitur untuk clustering\n",
    "clustering_features = merged_df[['total_quantity_sold', 'transaction_frequency', 'current_stock']]\n",
    "\n",
    "# Standarisasi data\n",
    "scaler = StandardScaler()\n",
    "scaled_features = scaler.fit_transform(clustering_features)\n",
    "\n",
    "# K-Means dengan 3 cluster\n",
    "kmeans = KMeans(n_clusters=3, random_state=42)\n",
    "merged_df['cluster'] = kmeans.fit_predict(scaled_features)\n",
    "\n",
    "# Menentukan label berdasarkan rata-rata penjualan tiap cluster\n",
    "cluster_means = merged_df.groupby('cluster')['total_quantity_sold'].mean().sort_values()\n",
    "cluster_mapping = {\n",
    "    cluster_means.index[0]: 'Low',\n",
    "    cluster_means.index[1]: 'Medium',\n",
    "    cluster_means.index[2]: 'High'\n",
    "}\n",
    "merged_df['movement_category'] = merged_df['cluster'].map(cluster_mapping)\n",
    "\n",
    "print(\"Distribusi Pergerakan Produk:\")\n",
    "print(merged_df['movement_category'].value_counts())\n",
    "\n",
    "# Visualisasi hasil clustering\n",
    "plt.figure(figsize=(10, 6))\n",
    "sns.scatterplot(x='current_stock', y='total_quantity_sold', hue='movement_category', data=merged_df, palette={'Low':'red', 'Medium':'orange', 'High':'green'})\n",
    "plt.title('Clustering Produk Berdasarkan Penjualan')\n",
    "plt.xlabel('Stok Saat Ini')\n",
    "plt.ylabel('Total Terjual')\n",
    "plt.show()"
   ]
  },
  {
   "cell_type": "markdown",
   "metadata": {},
   "source": [
    "## 6. Simple Moving Average (SMA)\n",
    "Menghitung estimasi kebutuhan stok berdasarkan rata-rata penjualan. Jika estimasi kebutuhan (misal untuk 7 hari) + stok minimal lebih besar dari stok saat ini, maka perlu restock."
   ]
  },
  {
   "cell_type": "code",
   "execution_count": None,
   "metadata": {},
   "outputs": [],
   "source": [
    "# Estimasi kebutuhan (demand) untuk 7 hari ke depan\n",
    "sma_period = 7\n",
    "merged_df['estimated_demand_7d'] = np.ceil(merged_df['avg_daily_sales'] * sma_period)\n",
    "\n",
    "# Rekomendasi Restock\n",
    "# Rumus: Kebutuhan 7 hari + Minimum Stock - Stok Saat ini\n",
    "merged_df['restock_recommendation'] = merged_df.apply(\n",
    "    lambda row: max(0, (row['estimated_demand_7d'] + row['minimum_stock']) - row['current_stock']), \n",
    "    axis=1\n",
    ")\n",
    "\n",
    "# Tampilkan produk yang butuh restock\n",
    "restock_needed = merged_df[merged_df['restock_recommendation'] > 0].sort_values(by='restock_recommendation', ascending=False)\n",
    "restock_needed[['product_name', 'current_stock', 'estimated_demand_7d', 'minimum_stock', 'restock_recommendation']].head(10)"
   ]
  },
  {
   "cell_type": "markdown",
   "metadata": {},
   "source": [
    "## 7. Simpan Hasil Analisis\n",
    "Menyimpan data hasil klasifikasi K-Means dan rekomendasi restock SMA."
   ]
  },
  {
   "cell_type": "code",
   "execution_count": None,
   "metadata": {},
   "outputs": [],
   "source": [
    "output_columns = [\n",
    "    'product_code', 'product_name', 'category', 'current_stock', 'minimum_stock',\n",
    "    'total_quantity_sold', 'transaction_frequency', 'avg_daily_sales',\n",
    "    'movement_category', 'restock_recommendation'\n",
    "]\n",
    "final_results = merged_df[output_columns]\n",
    "\n",
    "# Menyimpan hasil\n",
    "output_path = '../datasets/product_analysis_results.csv'\n",
    "final_results.to_csv(output_path, index=False)\n",
    "print(f\"Analisis selesai. Hasil disimpan di: {output_path}\")\n",
    "\n",
    "final_results.head()"
   ]
  }
 ],
 "metadata": {
  "kernelspec": {
   "display_name": "Python 3",
   "language": "python",
   "name": "python3"
  },
  "language_info": {
   "codemirror_mode": {
    "name": "ipython",
    "version": 3
   },
   "file_extension": ".py",
   "mimetype": "text/x-python",
   "name": "python",
   "nbconvert_exporter": "python",
   "pygments_lexer": "ipython3",
   "version": "3.8.5"
  }
 },
 "nbformat": 4,
 "nbformat_minor": 4
}

with open(r'c:\Users\Office\Developer\Projects\Laravel\BisnisKu\analytics\notebooks\product_analysis_notebook.ipynb', 'w') as f:
    json.dump(notebook, f, indent=1)
