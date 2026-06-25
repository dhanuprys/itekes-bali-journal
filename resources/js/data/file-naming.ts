/**
 * Centralized file naming convention configuration.
 * 
 * All naming hints and examples are defined here so that if the client
 * changes the convention, you only need to update this single file.
 */

export interface NamingHint {
    format: string;
    example: string;
}

// --- Common fragments used across hints ---
const EXAMPLE_PRODI = 'Sarjana Teknologi Pangan';
const EXAMPLE_NAMA = 'Nadya Tresna';

// --- Research / Community Service (3-part: Prodi_Nama_Context) ---

const researchPKMHint = (context: string): NamingHint => ({
    format: `Program Studi_Nama Ketua_${context}`,
    example: `${EXAMPLE_PRODI}_${EXAMPLE_NAMA}_${context}`,
});

export const NamingHints = {
    // Research Proposals
    RESEARCH_PROPOSAL: researchPKMHint('Proposal Penelitian'),
    RESEARCH_PROPOSAL_REVISION: researchPKMHint('Revisi Proposal Penelitian'),
    RESEARCH_PROGRESS_REPORT: researchPKMHint('Laporan Kemajuan Penelitian'),
    RESEARCH_FINAL_REPORT: researchPKMHint('Laporan Akhir Penelitian'),

    // Community Service (PKM) Proposals
    CS_PROPOSAL: researchPKMHint('Proposal PKM'),
    CS_PROPOSAL_REVISION: researchPKMHint('Revisi Proposal PKM'),
    CS_PROGRESS_REPORT: researchPKMHint('Laporan Kemajuan PKM'),
    CS_FINAL_REPORT: researchPKMHint('Laporan Akhir PKM'),

    // Shared (same for Research & PKM)
    MANUSCRIPT: researchPKMHint('Manuskrip Akhir'),
    SUPPLEMENTARY: researchPKMHint('File Luaran'),

    // Ethical Clearance (2-part: TemplateName_NamaPengaju)
    ethicsTemplate: (templateName: string): NamingHint => ({
        format: `${templateName}_Nama Pengaju`,
        example: `${templateName}_${EXAMPLE_NAMA}`,
    }),
} as const;

/**
 * Helper to pick the correct naming hint based on type and mode.
 */
export function getProposalHint(type: 'research' | 'community-service', mode: 'create' | 'revise'): NamingHint {
    if (type === 'research') {
        return mode === 'revise' ? NamingHints.RESEARCH_PROPOSAL_REVISION : NamingHints.RESEARCH_PROPOSAL;
    }
    return mode === 'revise' ? NamingHints.CS_PROPOSAL_REVISION : NamingHints.CS_PROPOSAL;
}

export function getProgressReportHint(type: 'research' | 'community-service'): NamingHint {
    return type === 'research' ? NamingHints.RESEARCH_PROGRESS_REPORT : NamingHints.CS_PROGRESS_REPORT;
}

export function getFinalReportHint(type: 'research' | 'community-service'): NamingHint {
    return type === 'research' ? NamingHints.RESEARCH_FINAL_REPORT : NamingHints.CS_FINAL_REPORT;
}
