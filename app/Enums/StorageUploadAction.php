<?php

namespace App\Enums;

enum StorageUploadAction: string
{
    case RESEARCH_PROPOSAL = 'a804ab864c3996e09ba0';
    case RESEARCH_PROGRESS_REPORT = 'fd91723937c849b46a84';
    case RESEARCH_FINAL_REPORT = 'cc45184279b9aab8b97d';
    case RESEARCH_MANUSCRIPT = 'b86deb10631d9a6eb09d';
    case CS_PROPOSAL = '3d21ece6ac8e376caa84';
    case CS_PROGRESS_REPORT = '9f5de0e023b7060320ab';
    case CS_FINAL_REPORT = 'a9b8cd4e294711823ab2';
    case CS_MANUSCRIPT = '51a13ef99f17d459855c';
    case RESEARCH_SUPPLEMENTARY = 'e9f1a2b3c4d5e6f7a8b9';
    case CS_SUPPLEMENTARY = 'f0a1b2c3d4e5f6a7b8c9';
    case ETHICS_PROPOSAL = 'e7b3a91f45c820d69e14';
    case ETHICS_OUTPUT = 'f4d2c8e716a930b58c23';
    case ETHICS_PAYMENT_PROOF = 'c1d2e3f4a5b6c7d8e9f0';
    case USER_PROFILE_PHOTO = 'fa125c14309328d8231c';
}
