import type { Category } from './category';

export interface Pictogram {
    id: number;
    category_id: number | null;
    name: string;
    description: string | null;
    image_url: string | null;
    icon_text: string | null;
    audio_url: string | null;
    difficulty_level: number;
    is_active: boolean;
    category?: Category;
}
