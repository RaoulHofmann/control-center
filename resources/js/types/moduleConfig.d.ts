export interface SchemaFieldBase {
  type: string;
  required: boolean;
  label: string;
  description: string;
}

export interface SelectFieldSchema extends SchemaFieldBase {
  type: "select";
  options: string[];
}

export interface MultiSelectFieldSchema extends SchemaFieldBase {
  type: "multiselect";
  options: string[];
  dynamic_options: boolean;
}

export type FieldSchema = SelectFieldSchema | MultiSelectFieldSchema;

export interface DiskUsageConfigSchema {
  source_type: SelectFieldSchema;
  selected_disks: MultiSelectFieldSchema;
}

export interface ModuleConfigSchema {
  [key: string]: FieldSchema;
}
