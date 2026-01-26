import { AlertCircleIcon, CheckCircleIcon, ClockIcon, FileTextIcon } from 'lucide-svelte';

export function getStatusConfig(status: string) {
    switch (status) {
        case 'approved':
            return { color: 'bg-green-500/10 text-green-600 hover:bg-green-500/20 border-green-200', label: 'Disetujui', icon: CheckCircleIcon };
        case 'rejected':
            return { color: 'bg-red-500/10 text-red-600 hover:bg-red-500/20 border-red-200', label: 'Ditolak', icon: AlertCircleIcon };
        case 'revision_needed':
            return {
                color: 'bg-orange-500/10 text-orange-600 hover:bg-orange-500/20 border-orange-200',
                label: 'Perlu Revisi',
                icon: AlertCircleIcon,
            };
        case 'need_review':
            return { color: 'bg-blue-500/10 text-blue-600 hover:bg-blue-500/20 border-blue-200', label: 'Menunggu Review', icon: ClockIcon };
        default:
            return { color: 'bg-muted text-muted-foreground', label: status.replace('_', ' ').toUpperCase(), icon: FileTextIcon };
    }
}
