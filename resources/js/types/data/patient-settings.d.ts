export interface PatientSetting {
    id: number;
    patient_id: number;
    tts_enabled: boolean;
    smooth_animations: boolean;
    stimulation_level: 'low' | 'medium' | 'high';
}
