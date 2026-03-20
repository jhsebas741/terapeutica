import type { Pictogram } from './pictogram';

export type GameType = 'emparejar' | 'secuencia' | 'emocion';

export interface Game {
    id: number;
    name: string;
    description: string | null;
    type: GameType;
    level: number;
    is_active: boolean;
    game_elements?: GameElement[];
    game_elements_count?: number;
    created_at?: string;
    updated_at?: string;
}

export interface GameElement {
    id?: number;
    game_id?: number;
    pictogram_id: number;
    situation_text?: string | null;
    sequence_order?: number | null;
    pictogram?: Pictogram;
}
