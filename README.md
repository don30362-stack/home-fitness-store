# Home Fitness Store

> 🚧 **Work in Progress** — 專案仍在開發中，尚無正式 Public Demo。

家用重訓器材電商專題，採前後端分離架構：前端為 Vue 3 + TypeScript SPA，後端由 Laravel 提供 REST API，並以 MySQL 儲存資料。

## Project Status

- 商品瀏覽、會員認證與地址管理已完成前後端串接。
- 匿名與會員購物車、登入後 Cart Merge 已實作；會員購物車儲存於資料庫。
- Checkout 已可建立訂單，後端會重新驗證商品、價格與庫存，建立訂單後扣除庫存並清空購物車。
- 會員訂單列表與訂單詳細查詢尚未實作；前端目前在 Checkout 頁內顯示建單成功結果。
- 首頁與品牌介紹仍為初步骨架，整體 UI / RWD 仍在整理。
- 開發資料部分由 Seeder / Factory 產生，包含縣市與行政區、測試分類、商品、圖片路徑、規格與部分商品 variants；會員 Factory 主要供自動化測試使用。

## Tech Stack

### Frontend

- Vue 3.5
- TypeScript 6
- Vite 8
- Vue Router 5
- Pinia 4
- Axios 1.19
- Bootstrap 5.3

### Backend

- Laravel 13
- PHP 8.3+
- Laravel Sanctum 4
- REST API
- PHPUnit 12 / Laravel Feature Tests

### Database & Development

- MySQL
- npm
- Composer
- Git

`frontend/package.json` 要求 Node.js `^22.18.0` 或 `>=24.12.0`。Repository 未將 XAMPP 設為必要依賴。

## Current Features

### Products

**Frontend**

- 大分類／子分類導覽、商品列表與商品詳細頁。
- 商品名稱搜尋、價格區間篩選、價格排序與分頁。
- 商品圖片畫廊、規格與庫存顯示、相關商品。

**Backend**

- 商品分類、分頁列表、單一商品與同分類相關商品 API。
- 查詢參數驗證，並支援分類、名稱、價格與排序條件。
- API Resource 回傳商品圖片、詳細規格與 variants。

### Authentication & Member

**Frontend**

- 會員註冊、登入、登出與登入狀態還原。
- 會員基本資料、密碼修改與需登入頁面的路由保護。
- 縣市／行政區連動選單，以及地址查詢、新增、修改、刪除與預設地址設定。

**Backend**

- 註冊、登入、登出與會員資料／密碼 API。
- Laravel Sanctum SPA 認證：CSRF cookie、session cookie 與 `auth:sanctum` 保護路由。
- 縣市、行政區 API。
- 會員地址 CRUD、所有權限制與預設地址切換邏輯。

### Cart

**Frontend**

- 匿名購物車以 `localStorage` 保留；會員購物車透過 API 讀取。
- 加入商品、調整數量、移除單項、清空購物車與購物車摘要。
- 登入後自動合併匿名購物車；合併失敗時保留 localStorage 資料並提供重試。
- 前端會限制數量並標示下架、停用規格或庫存不足的商品。

**Backend**

- 會員購物車與購物車項目儲存於資料庫。
- 查詢、新增、數量修改、移除、清空與 Cart Merge API。
- 商品／variant 對應、商品狀態、庫存、數量與購物車項目所有權驗證。
- Cart Merge 使用 database transaction，失敗時不留下部分合併結果。

### Checkout & Orders

**Frontend**

- Checkout 表單可使用已儲存地址或填寫新收件資料。
- 訂購人／收件人驗證、縣市／行政區選擇、宅配、貨到付款與模擬信用卡付款。
- 送出後顯示訂單編號、付款狀態、金額與收件資料。

**Backend**

- CheckoutRequest 進行 server-side validation，並限制可用的配送與付款方式。
- Checkout service 在 database transaction 內重新讀取並鎖定購物車、商品與 variants，重新驗證庫存與計算價格。
- 建立 orders / order_items，保留商品編號、名稱、variant 與價格 snapshot。
- 建單成功後扣除庫存並清空購物車；失敗時整筆 transaction rollback。
- 目前只有建單 API，尚無會員訂單列表與詳細查詢 API。

## Testing

後端使用 PHPUnit，目前實際測試包含：

- `LocationApiTest`：縣市列表、行政區篩選與無效縣市。
- `AddressApiTest`：認證保護、地址所有權、CRUD 驗證與預設地址邏輯。
- `ProfileApiTest`：會員資料、email / phone 驗證、密碼更新與認證保護。
- `CartApiTest`：購物車 CRUD、所有權、variants、庫存／數量限制、Cart Merge 與 rollback。
- `CheckoutApiTest`：輸入驗證、貨到付款／模擬信用卡、建單、snapshot、扣庫存、清購物車、重複送出與 rollback。
- 另有 Laravel 預設的基本 Unit / Feature example tests。

目前沒有商品 API、註冊／登入流程與訂單查詢的專屬自動化測試。

```bash
cd backend
php artisan test
```

## Project Structure

```text
home-fitness-store/
├── frontend/    # Vue SPA
└── backend/     # Laravel REST API
```

## Local Development

### Backend

1. 建立 MySQL 資料庫（預設名稱為 `home_fitness_store`），並在 `backend/.env` 設定連線資訊。
2. 安裝依賴、產生 application key，建立資料表與開發資料，然後啟動 API：

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Backend 預設網址：`http://127.0.0.1:8000`

`--seed` 用於建立目前商品瀏覽與地區選單所需的開發資料。

### Frontend

```bash
cd frontend
npm install
cp .env.example .env
npm run dev
```

Frontend 預設網址：`http://localhost:5173`

Windows PowerShell 可以 `Copy-Item .env.example .env` 取代 `cp`。

## API

前端透過 `VITE_API_BASE_URL` 設定 API base URL；目前 `frontend/.env.example` 為：

```dotenv
VITE_API_BASE_URL=http://127.0.0.1:8000/api
```

## Remaining Work / Roadmap

- 實作會員訂單列表與訂單詳細 API／頁面。
- 補齊商品、註冊／登入與訂單查詢的自動化測試。
- 完成首頁與品牌內容，並整理整體 UI / RWD。
