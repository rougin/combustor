<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * {{ name | title | replace({'_': ' '}) }} Model
 *
 * @package  CodeIgniter
 * @category Model
{% if type == 'doctrine' %}
 * 
 * @Entity
 * @Table(name="{{ name }}")
{% endif %}
 */
class {{ name | capitalize }} extends CI_Model {

{% for column in columns %}
    /**
{% if type == 'doctrine' %}
{% if column.ifPrimaryKey or column.isAutoIncrement %}
     * {{ column.isPrimaryKey ? '@Id ' : ''}}{{ column.isAutoIncrement ? '@GeneratedValue' : '' }}
{% endif %}
{% if column.isForeignKey %}
     * @ManyToOne(targetEntity="{{ column.referencedTable | capitalize }}", cascade={"persist"})
     * @JoinColumn(name="{{ column.field }}", referencedColumnName="{{ column.referencedField }}", nullable={{ column.isNull ? 'TRUE' : 'FALSE' }}, unique={{ column.isUnique ? 'TRUE' : 'FALSE' }}, onDelete="cascade")
{% else %}
     * @Column(name="{{ column.field }}", type="{{ column.dataType }}"{{ column.length ? ', length=10,' : ','}} nullable={{ column.isNull ? 'TRUE' : 'FALSE' }}, unique={{ column.isUnique ? 'TRUE' : 'FALSE' }})
{% endif %}
{% else %}
     * @var {{ column.dataType }}
{% endif %}
     */
    protected $_{{ column.field }};

{% endfor %}
{% for column in columns %}
{% set field = isCamel ? camel[column.field].field : underscore[column.field].field %}
{% set accessor = isCamel ? camel[column.field]['accessor'] : underscore[column.field]['accessor'] %}
{% set mutator = isCamel ? camel[column.field]['mutator'] : underscore[column.field]['mutator'] %}
    /**
     * Gets {{ column.field | lower | replace({'_': ' '}) }}
     *
     * @return {{ column.dataType }}
     */
    public function {{ accessor }}()
    {
        return $this->_{{ column.field }};
    }

    /**
     * Gets {{ column.field | lower | replace({'_': ' '}) }}
     *
     * @param  {{ column.dataType }}
     * @return {{ name | capitalize }}
     */
    public function {{ mutator }}({{ column.isForeignKey ? '\\' ~ column.referencedTable | capitalize ~ ' ' : '' }}${{ field }})
    {
{% if column.dataType == 'datetime' %}
        $this->_{{ column.field }} = new \DateTime(${{ field }});
{% else %}
        $this->_{{ column.field }} = ${{ field }};
{% endif %}

        return $this;
    }

{% endfor %}
{% if type == 'wildfire' %}
    /**
     * Saves the data to storage
     * 
     * @return boolean
     */
    public function save()
    {
        $data = array(
{% for column in columns if column.field != 'datetime_created' and column.field != 'datetime_updated' %}
{% set field = isCamel ? camel[column.field].field : underscore[column.field].field %}
{% set accessor = isCamel ? camel[column.field]['accessor'] : underscore[column.field]['accessor'] %}
{% if column.field in primaryKeys|keys %}
            '{{ column.field }}' => $this->{{ accessor }}()->{{ primaryKeys[column.field] }}(),
{% else %}
            '{{ column.field }}' => $this->{{ accessor }}(),
{% endif %}
{% endfor %}
        );
{% for column in columns if column.field == 'datetime_created' or column.field == 'datetime_updated' %}
{% set field = isCamel ? camel[column.field].field : underscore[column.field].field %}
{% set accessor = isCamel ? camel[column.field]['accessor'] : underscore[column.field]['accessor'] %}

        if ($this->_{{ column.field }})
        {
            $data['{{ column.field }}'] = $this->{{ accessor }}()->format('Y-m-d H:i:s');
        }
{% endfor %}

        if ($this->_{{ primaryKey }} > 0)
        {
            $this->db->where('{{ primaryKey }}', $this->_{{ primaryKey }});

            if ($this->db->get('{{ name }}')->num_rows())
            {
                if ($this->db->update('{{ name }}', $data, array('{{ primaryKey }}' => $this->_{{ primaryKey }})))
                {
                    return TRUE;
                }
            }
            else if ($this->db->insert('{{ name }}', $data))
            {
                return TRUE;
            }
        }
        else if ($this->db->insert('{{ name }}', $data))
        {
            $this->_{{ primaryKey }} = $this->db->insert_id();
            
            return TRUE;
        }

        return FALSE;
    }

{% endif %}
}