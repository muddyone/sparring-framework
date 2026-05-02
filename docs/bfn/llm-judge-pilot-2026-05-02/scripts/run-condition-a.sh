#!/usr/bin/env bash
# Condition A: single-agent baseline. One Anthropic API call with the decision pack + structured-output prompt.
# Usage: ./run-condition-a.sh <decision-pack-path> <output-path>

set -euo pipefail

DECISION_PACK="${1:?usage: run-condition-a.sh <decision-pack-path> <output-path>}"
OUTPUT_PATH="${2:?usage: run-condition-a.sh <decision-pack-path> <output-path>}"

# Source local lifspel .env to get ANTHROPIC_API_KEY
set -a
# shellcheck disable=SC1091
source /home/bartniedner/projects/lifspel/.env
set +a

PACK_CONTENT="$(cat "$DECISION_PACK")"

SYSTEM_PROMPT='You are advising a partner team on a real, currently-pending product decision. The team needs a recommendation they can act on. Your output must be:

1. A single-paragraph RECOMMENDATION (~120-180 words): name the chosen option, give the main rationale tying it to the evidence in the decision pack.

2. A bulleted KEY CONCERNS list (5-8 items, each 1-2 sentences): real risks the decision-maker should know about, grounded in the evidence base. Cite specific evidence where applicable.

3. A CONFIDENCE statement: low / medium / high, with one sentence justifying the level.

Total length target: 400-600 words. Use plain prose; no headers beyond RECOMMENDATION / KEY CONCERNS / CONFIDENCE labels. Do not include preamble, meta-commentary, or self-reference. Do not bracket your output with "Here is my recommendation:" etc.'

REQUEST_JSON="$(jq -n \
  --arg model "claude-opus-4-7" \
  --arg system "$SYSTEM_PROMPT" \
  --arg user "$PACK_CONTENT" \
  '{
    model: $model,
    max_tokens: 1500,
    system: $system,
    messages: [{role: "user", content: $user}]
  }')"

mkdir -p "$(dirname "$OUTPUT_PATH")"

RESPONSE="$(curl -sS https://api.anthropic.com/v1/messages \
  -H "x-api-key: $ANTHROPIC_API_KEY" \
  -H "anthropic-version: 2023-06-01" \
  -H "content-type: application/json" \
  -d "$REQUEST_JSON")"

# Save the full response (with usage data) and a clean text-only version
echo "$RESPONSE" > "${OUTPUT_PATH}.raw.json"
echo "$RESPONSE" | jq -r '.content[0].text' > "$OUTPUT_PATH"

# Report usage
INPUT_TOKENS="$(echo "$RESPONSE" | jq -r '.usage.input_tokens // "?"')"
OUTPUT_TOKENS="$(echo "$RESPONSE" | jq -r '.usage.output_tokens // "?"')"
echo "Saved to: $OUTPUT_PATH"
echo "Tokens: input=$INPUT_TOKENS output=$OUTPUT_TOKENS"
