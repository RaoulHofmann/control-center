import {ModuleConfigSchema} from "@/types/moduleConfig";

export * from "./modules/series"

export interface Module {
    id: number,
    name: string,
    type: string,
    description: string,
    is_active: boolean,
    config_schema: ModuleConfigSchema,
    created_at: string,
    updated_at: string
}

export interface ModuleInstance {
    id: number,
    module_id: number,
    name: string,
    is_active: boolean,
    display_order: number,
    config: any,
    cached_data: any,
    last_updated_at: string,
    created_at: string,
    updated_at: string,
    module: Module
}

export interface DiskSpace {
    path: string,
    freeSpace: number,
    totalSpace: number
}
