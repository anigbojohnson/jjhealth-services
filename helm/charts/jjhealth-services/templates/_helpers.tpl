{{/* Generate the chart name */}}
{{- define "jjhealth.name" -}}
{{- default .Chart.Name .Values.nameOverride | trunc 63 | trimSuffix "-" -}}
{{- end -}}

{{/* Generate a fully qualified name (release + chart) */}}
{{- define "jjhealth.fullname" -}}
{{- printf "%s-%s" .Release.Name (include "jjhealth.name" .) | trunc 63 | trimSuffix "-" -}}
{{- end -}}

{{/* Common labels used by all resources */}}
{{- define "jjhealth.labels" -}}
helm.sh/chart: {{ include "jjhealth.name" . }}-{{ .Chart.Version | replace "+" "_" }}
app.kubernetes.io/name: {{ include "jjhealth.name" . }}
app.kubernetes.io/instance: {{ .Release.Name }}
app.kubernetes.io/version: {{ .Chart.AppVersion }}
app.kubernetes.io/managed-by: {{ .Release.Service }}
{{- end -}}


{{/* Common selector labels */}}
{{- define "jjhealth.selectorLabels" -}}
app.kubernetes.io/name: {{ include "jjhealth.name" . }}
app.kubernetes.io/instance: {{ .Release.Name }}
{{- end -}}
