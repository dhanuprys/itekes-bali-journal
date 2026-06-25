import { clsx, type ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export type WithoutChild<T> = T extends { child?: any } ? Omit<T, 'child'> : T;

export type WithoutChildren<T> = T extends { children?: any } ? Omit<T, 'children'> : T;
export type WithoutChildrenOrChild<T> = WithoutChildren<WithoutChild<T>>;
export type WithElementRef<T, U extends HTMLElement = HTMLElement> = T & { ref?: U | null };

/**
 * Converts a snake_case string to Title Case.
 * Example: 'surat_pengantar' -> 'Surat Pengantar'
 */
export function snakeToTitleCase(str: string): string {
    if (!str) return '';
    return str
        .split('_')
        .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
}

