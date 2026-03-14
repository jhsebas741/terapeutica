export interface Category {
    id: number;
    name: string;
    description: string | null;
    icon_emoji: string | null;
    color_hex: string;
    pictograms_count?: number;
}
