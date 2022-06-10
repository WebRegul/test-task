const issues = [
    {
        id: 1,
        key: 'R-1',
        fields: {
            summary: {
                type: 'string',
                value: 'Архитектура и инфраструктура'
            },
            description: {
                type: 'text',
                value: ''
            },
            author: 'root',
            created: '2021-10-21T11:03:00.000Z',
            updated: '2021-10-21T11:10:00.000Z',
            updatedBy: 'root',
            State: {
                type: 'state',
                value: 'Open'
            },
            Type: {
                type: 'enum',
                value: 'User Story'
            }
        },
        history: []
    },
    {
        id: 2,
        key: 'R-2',
        fields: {
            summary: {
                type: 'string',
                value: 'Проектирование архитектуры проекта'
            },
            description: {
                type: 'text',
                value: 'подготовить архитектуру проекта'
            },
            author: 's.benfetima',
            created: '2021-10-21T14:22:00.000Z',
            updated: '2021-10-21T14:22:00.000Z',
            updatedBy: 's.benfetima',
            State: {
                type: 'state',
                value: 'Done'
            },
            Type: {
                type: 'enum',
                value: 'Task'
            },
            'Исполнитель': {
                type: 'user',
                value: 'root'
            },
            links: [{
                linkName: 'Subtask',
                target: '1'
            }],
        },
        history: [
            {
                id: '2-1',
                author: 'root',
                timestamp: '2021-10-23T13:54:00.000Z',
                fieldChanges: {
                    State: {
                        removedValues: {
                            type: 'state',
                            value: 'Open'
                        },
                        addedValues: {
                            type: 'state',
                            value: 'Done'
                        }
                    }
                }
            }
        ]
    },
    {
        id: 3,
        key: 'R-3',
        fields: {
            summary: {
                type: 'string',
                value: 'Проектирование инфрастуктуры проекта'
            },
            description: {
                type: 'text',
                value: 'подготовить схему инфраструктуры проекта'
            },
            author: 's.benfetima',
            created: '2021-10-21T14:40:00.000Z',
            updated: '2021-10-21T14:41:00.000Z',
            updatedBy: 's.benfetima',
            State: {
                type: 'state',
                value: 'Done'
            },
            Type: {
                type: 'enum',
                value: 'Task'
            },
            'Исполнитель': {
                type: 'user',
                value: 'root'
            },
            links: [{
                linkName: 'Subtask',
                target: '1'
            }],
        },
        history: [
            {
                id: '3-1',
                author: 'root',
                timestamp: '2021-10-23T19:32:00.000Z',
                fieldChanges: {
                    State: {
                        removedValues: {
                            type: 'state',
                            value: 'Open'
                        },
                        addedValues: {
                            type: 'state',
                            value: 'Done'
                        }
                    }
                }
            }
        ]
    },
    {
        id: 4,
        key: 'R-4',
        fields: {
            summary: {
                type: 'string',
                value: 'Проектирование базы данных основного сервиса'
            },
            description: {
                type: 'text',
                value: ''
            },
            author: 's.benfetima',
            created: '2021-10-21T14:51:00.000Z',
            updated: '2021-10-21T14:51:00.000Z',
            updatedBy: 's.benfetima',
            State: {
                type: 'state',
                value: 'Done'
            },
            Type: {
                type: 'enum',
                value: 'Task'
            },
            'Исполнитель': {
                type: 'user',
                value: 'root'
            },
            links: [{
                linkName: 'Subtask',
                target: '1'
            }],
        },
        history: [
            {
                id: '4-1',
                author: 'root',
                timestamp: '2021-10-24T12:20:00.000Z',
                fieldChanges: {
                    State: {
                        removedValues: {
                            type: 'state',
                            value: 'Open'
                        },
                        addedValues: {
                            type: 'state',
                            value: 'Done'
                        }
                    }
                }
            }
        ]
    },
    {
        id: 5,
        key: 'R-5',
        fields: {
            summary: {
                type: 'string',
                value: 'Проектирование базы данных админ-сервиса'
            },
            description: {
                type: 'text',
                value: ''
            },
            author: 's.benfetima',
            created: '2021-10-21T14:56:00.000Z',
            updated: '2021-10-24T16:40:00.000Z',
            updatedBy: 'root',
            State: {
                type: 'state',
                value: 'Done'
            },
            Type: {
                type: 'enum',
                value: 'Task'
            },
            'Исполнитель': {
                type: 'user',
                value: 'root'
            },
            links: [{
                linkName: 'Subtask',
                target: '1'
            }],
        },
        history: [
            {
                id: '5-1',
                author: 'root',
                timestamp: '2021-10-24T16:40:00.000Z',
                fieldChanges: {
                    State: {
                        removedValues: {
                            type: 'state',
                            value: 'Open'
                        },
                        addedValues: {
                            type: 'state',
                            value: 'Done'
                        }
                    }
                }
            }
        ]
    },
    {
        id: 6,
        key: 'R-6',
        fields: {
            summary: {
                type: 'string',
                value: 'Проектирование базы данных контентного сервиса'
            },
            description: {
                type: 'text',
                value: ''
            },
            author: 's.benfetima',
            created: '2021-10-21T14:59:00.000Z',
            updated: '2021-10-24T21:52:00.000Z',
            updatedBy: 'root',
            State: {
                type: 'state',
                value: 'Done'
            },
            Type: {
                type: 'enum',
                value: 'Task'
            },
            'Исполнитель': {
                type: 'user',
                value: 'root'
            },
            links: [{
                linkName: 'Subtask',
                target: '1'
            }],
        },
        history: [
            {
                id: '6-1',
                author: 'root',
                timestamp: '2021-10-24T21:52:00.000Z',
                fieldChanges: {
                    State: {
                        removedValues: {
                            type: 'state',
                            value: 'Open'
                        },
                        addedValues: {
                            type: 'state',
                            value: 'Done'
                        }
                    }
                }
            }
        ]
    },
    {
        id: 7,
        key: 'R-7',
        fields: {
            summary: {
                type: 'string',
                value: 'Проектирование оркестрации между сервисами'
            },
            description: {
                type: 'text',
                value: ''
            },
            author: 's.benfetima',
            created: '2021-10-21T15:05:00.000Z',
            updated: '2021-10-24T23:56:00.000Z',
            updatedBy: 'root',
            State: {
                type: 'state',
                value: 'Done'
            },
            Type: {
                type: 'enum',
                value: 'Task'
            },
            'Исполнитель': {
                type: 'user',
                value: 'root'
            },
            links: [{
                linkName: 'Subtask',
                target: '1'
            }],
        },
        history: [
            {
                id: '7-1',
                author: 'root',
                timestamp: '2021-10-24T23:56:00.000Z',
                fieldChanges: {
                    State: {
                        removedValues: {
                            type: 'state',
                            value: 'Open'
                        },
                        addedValues: {
                            type: 'state',
                            value: 'Done'
                        }
                    }
                }
            }
        ]
    },

    {
        id: 8,
        key: 'R-8',
        fields: {
            summary: {
                type: 'string',
                value: 'Админка'
            },
            description: {
                type: 'text',
                value: ''
            },
            author: 'root',
            created: '2021-10-21T11:03:10.000Z',
            updated: '2021-10-21T11:03:10.000Z',
            updatedBy: 'root',
            State: {
                type: 'state',
                value: 'Open'
            },
            Type: {
                type: 'enum',
                value: 'User Story'
            }
        },
        history: []
    },
    {
        id: 9,
        key: 'R-9',
        fields: {
            summary: {
                type: 'string',
                value: 'Методы авторизации в админке'
            },
            description: {
                type: 'text',
                value: 'Подготовить методы для авторизации в админке.\\n\\nВсе по аналогии с основным микросервисом, авторизация так же токеном. Но таблица юзеров тут своя.\\n\\nРегистрация и восстановление пароля пока не нужны'
            },
            author: 's.benfetima',
            created: '2021-10-26T10:15:00.000Z',
            updated: '2021-10-29T13:40:00.000Z',
            updatedBy: 's.benfetima',
            State: {
                type: 'state',
                value: 'Verified'
            },
            Type: {
                type: 'enum',
                value: 'Task'
            },
            'Исполнитель': {
                type: 'user',
                value: 'gagafonov'
            },
            links: [{
                linkName: 'Subtask',
                target: '8'
            }],
        },
        history: [
            {
                id: '9-1',
                author: 'gagafonov',
                timestamp: '2021-10-29T11:54:00.000Z',
                fieldChanges: {
                    State: {
                        removedValues: {
                            type: 'state',
                            value: 'Open'
                        },
                        addedValues: {
                            type: 'state',
                            value: 'To Verify'
                        }
                    }
                }
            },
            {
                id: '9-2',
                author: 's.benfetima',
                timestamp: '2021-10-29T13:40:00.000Z',
                fieldChanges: {
                    State: {
                        removedValues: {
                            type: 'state',
                            value: 'To Verify'
                        },
                        addedValues: {
                            type: 'state',
                            value: 'Verified'
                        }
                    }
                }
            }
        ]
    },
    {
        id: 10,
        key: 'R-10',
        fields: {
            summary: {
                type: 'string',
                value: 'Реализация миграций'
            },
            description: {
                type: 'text',
                value: 'Реализовать миграции БД для сервиса админки'
            },
            author: 's.benfetima',
            created: '2021-10-26T10:30:00.000Z',
            updated: '2021-10-29T15:28:00.000Z',
            updatedBy: 'gagafonov',
            State: {
                type: 'state',
                value: 'Verified'
            },
            Type: {
                type: 'enum',
                value: 'Task'
            },
            'Исполнитель': {
                type: 'user',
                value: 'gagafonov'
            },
            links: [{
                linkName: 'Subtask',
                target: '8'
            }],
        },
        history: [
            {
                id: '10-1',
                author: 'gagafonov',
                timestamp: '2021-10-29T13:09:00.000Z',
                fieldChanges: {
                    State: {
                        removedValues: {
                            type: 'state',
                            value: 'Open'
                        },
                        addedValues: {
                            type: 'state',
                            value: 'To Verify'
                        }
                    }
                }
            },
            {
                id: '10-2',
                author: 'gagafonov',
                timestamp: '2021-10-29T15:28:00.000Z',
                fieldChanges: {
                    State: {
                        removedValues: {
                            type: 'state',
                            value: 'To Verify'
                        },
                        addedValues: {
                            type: 'state',
                            value: 'Verified'
                        }
                    }
                }
            }
        ]
    },
    {
        id: 11,
        key: 'R-11',
        fields: {
            summary: {
                type: 'string',
                value: 'Управление городами'
            },
            description: {
                type: 'text',
                value: 'Реализовать методы для управления городами:\\n\\n1. добавление\\n2. удаление\\n3. обновление\\n\\nМетод обновления дополнительно на вход должен принимать порядок сортировки города и признак активности'
            },
            author: 's.benfetima',
            created: '2021-10-26T10:54:00.000Z',
            updated: '2021-10-29T18:53:00.000Z',
            updatedBy: 's.benfetima',
            State: {
                type: 'state',
                value: 'Verified'
            },
            Type: {
                type: 'enum',
                value: 'Task'
            },
            'Исполнитель': {
                type: 'user',
                value: 'gagafonov'
            },
            links: [{
                linkName: 'Subtask',
                target: '8'
            }],
        },
        history: [
            {
                id: '11-1',
                author: 'gagafonov',
                timestamp: '2021-10-29T11:32:00.000Z',
                fieldChanges: {
                    State: {
                        removedValues: {
                            type: 'state',
                            value: 'Open'
                        },
                        addedValues: {
                            type: 'state',
                            value: 'To Verify'
                        }
                    }
                }
            },
            {
                id: '11-2',
                author: 's.benfetima',
                timestamp: '2021-10-29T18:53:00.000Z',
                fieldChanges: {
                    State: {
                        removedValues: {
                            type: 'state',
                            value: 'To Verify'
                        },
                        addedValues: {
                            type: 'state',
                            value: 'Verified'
                        }
                    }
                }
            }
        ]
    },
    {
        id: 12,
        key: 'R-12',
        fields: {
            summary: {
                type: 'string',
                value: 'Реализация методов создания, обновления и удаления кладбища'
            },
            description: {
                type: 'text',
                value: 'Реализовать методы для управления кладбищами:\\n\\n1. добавление\\n2. удаление\\n3. обновление\\n\\nМетод удаления должен учитывать проверки на использование данного кладбища агентами. В случае если кладбище используется, запрещать его удаление. Так же метод принимает идентификатор города с проверкой на его существование в БД. Хранение кладбищ на стороне основного сервиса, запрос проксирующий.'
            },
            author: 's.benfetima',
            created: '2021-10-26T11:35:00.000Z',
            updated: '2021-11-04T13:57:00.000Z',
            updatedBy: 'gagafonov',
            State: {
                type: 'state',
                value: 'To Verify'
            },
            Type: {
                type: 'enum',
                value: 'Task'
            },
            'Исполнитель': {
                type: 'user',
                value: 'gagafonov'
            },
            links: [{
                linkName: 'Subtask',
                target: '8'
            }],
        },
        history: [
            {
                id: '12-1',
                author: 'gagafonov',
                timestamp: '2021-11-04T13:57:00.000Z',
                fieldChanges: {
                    State: {
                        removedValues: {
                            type: 'state',
                            value: 'Open'
                        },
                        addedValues: {
                            type: 'state',
                            value: 'To Verify'
                        }
                    }
                }
            }
        ]
    },
    {
        id: 13,
        key: 'R-13',
        fields: {
            summary: {
                type: 'string',
                value: 'Реализация прокси-методов управления пользователями'
            },
            description: {
                type: 'text',
                value: 'Реализовать методы для управления пользователями:\\n\\n1. список\\n2. активация/деактивация\\n3. получение информации о пользователе\\n\\nВсе методы проксирующие к основному сервису, непосредственная реализация БЛ в main-service.'
            },
            author: 's.benfetima',
            created: '2021-10-26T11:58:00.000Z',
            updated: '2021-11-04T16:03:00.000Z',
            updatedBy: 'gagafonov',
            State: {
                type: 'state',
                value: 'To Verify'
            },
            Type: {
                type: 'enum',
                value: 'Task'
            },
            'Исполнитель': {
                type: 'user',
                value: 'gagafonov'
            },
            links: [{
                linkName: 'Subtask',
                target: '8'
            }],
        },
        history: [
            {
                id: '13-1',
                author: 'gagafonov',
                timestamp: '2021-11-04T16:03:00.000Z',
                fieldChanges: {
                    State: {
                        removedValues: {
                            type: 'state',
                            value: 'Open'
                        },
                        addedValues: {
                            type: 'state',
                            value: 'To Verify'
                        }
                    }
                }
            }
        ]
    },
    {
        id: 14,
        key: 'R-14',
        fields: {
            summary: {
                type: 'string',
                value: 'Реализация авторизации под пользователем'
            },
            description: {
                type: 'text',
                value: 'Метод должен осущесвить админ-запрос на авторизацию в main-service. Передается идентификатор пользователя, пользователя принудительно авторизуется в основном сервисе и в админский сервис возвращается токен, который возвращается на фронт. Дальнейшая логика будет на стороне фронта'
            },
            author: 's.benfetima',
            created: '2021-10-26T12:35:00.000Z',
            updated: '2021-11-07T11:42:00.000Z',
            updatedBy: 'gagafonov',
            State: {
                type: 'state',
                value: 'To Verify'
            },
            Type: {
                type: 'enum',
                value: 'Task'
            },
            'Исполнитель': {
                type: 'user',
                value: 'gagafonov'
            },
            links: [{
                linkName: 'Subtask',
                target: '8'
            }],
        },
        history: [
            {
                id: '14-1',
                author: 'gagafonov',
                timestamp: '2021-11-07T11:42:00.000Z',
                fieldChanges: {
                    State: {
                        removedValues: {
                            type: 'state',
                            value: 'Open'
                        },
                        addedValues: {
                            type: 'state',
                            value: 'To Verify'
                        }
                    }
                }
            }
        ]
    },
    {
        id: 15,
        key: 'R-15',
        fields: {
            summary: {
                type: 'string',
                value: 'Реализация методов управления доступными услугами'
            },
            description: {
                type: 'text',
                value: 'Реализовать методы добавления, обновления и удаления доступных услуг. При добавлении и редактировании на вход принимается название, описание и статус. При удалении необходимо реализовать проверку на использование услуги пользователями. Методы проксирующие, реализация БЛ на стороне main-service'
            },
            author: 's.benfetima',
            created: '2021-10-27T16:15:00.000Z',
            updated: '2021-11-10T14:13:00.000Z',
            updatedBy: 'gagafonov',
            State: {
                type: 'state',
                value: 'To Verify'
            },
            Type: {
                type: 'enum',
                value: 'Task'
            },
            'Исполнитель': {
                type: 'user',
                value: 'gagafonov'
            },
            links: [{
                linkName: 'Subtask',
                target: '8'
            }],
        },
        history: [
            {
                id: '15-1',
                author: 'gagafonov',
                timestamp: '2021-11-10T14:13:00.000Z',
                fieldChanges: {
                    State: {
                        removedValues: {
                            type: 'state',
                            value: 'Open'
                        },
                        addedValues: {
                            type: 'state',
                            value: 'To Verify'
                        }
                    }
                }
            }
        ]
    },

    {
        id: 16,
        key: 'R-16',
        fields: {
            summary: {
                type: 'string',
                value: 'Контентный сервис'
            },
            description: {
                type: 'text',
                value: ''
            },
            author: 'root',
            created: '2021-10-21T12:13:10.000Z',
            updated: '2021-10-21T12:13:10.000Z',
            updatedBy: 'root',
            State: {
                type: 'state',
                value: 'Open'
            },
            Type: {
                type: 'enum',
                value: 'User Story'
            }
        },
        history: []
    },
    {
        id: 17,
        key: 'R-17',
        fields: {
            summary: {
                type: 'string',
                value: 'Реализация основы контентного сервиса'
            },
            description: {
                type: 'text',
                value: 'Реализовать базовую основу котентного сервиса. Внедрить основные методы межсервисного взаимодействия, админские мидлвары и токен'
            },
            author: 's.benfetima',
            created: '2021-11-04T11:23:00.000Z',
            updated: '2021-11-09T18:47:00.000Z',
            updatedBy: 'root',
            State: {
                type: 'state',
                value: 'Verified'
            },
            Type: {
                type: 'enum',
                value: 'Task'
            },
            'Исполнитель': {
                type: 'user',
                value: 'a.grober'
            },
            links: [{
                linkName: 'Subtask',
                target: '16'
            }],
        },
        history: [
            {
                id: '17-1',
                author: 'a.grober',
                timestamp: '2021-11-09T16:03:00.000Z',
                fieldChanges: {
                    State: {
                        removedValues: {
                            type: 'state',
                            value: 'Open'
                        },
                        addedValues: {
                            type: 'state',
                            value: 'To Verify'
                        }
                    }
                }
            },
            {
                id: '17-2',
                author: 'root',
                timestamp: '2021-11-09T18:47:00.000Z',
                fieldChanges: {
                    State: {
                        removedValues: {
                            type: 'state',
                            value: 'To Verify'
                        },
                        addedValues: {
                            type: 'state',
                            value: 'Verified'
                        }
                    }
                }
            }
        ]
    },
    {
        id: 18,
        key: 'R-18',
        fields: {
            summary: {
                type: 'string',
                value: 'Гайды. Метод получения списка гайдов'
            },
            description: {
                type: 'text',
                value: 'Реализовать для получения списка гайдов. В списке возвращается название, даты создания и обновения, идентификатор, статус. Список должен быть реализовать с базовой пагинацией. Сортировка по дате по убыванию'
            },
            author: 's.benfetima',
            created: '2021-11-04T13:12:00.000Z',
            updated: '2021-11-10T11:24:00.000Z',
            updatedBy: 'root',
            State: {
                type: 'state',
                value: 'Verified'
            },
            Type: {
                type: 'enum',
                value: 'Task'
            },
            'Исполнитель': {
                type: 'user',
                value: 'a.grober'
            },
            links: [{
                linkName: 'Subtask',
                target: '16'
            }],
        },
        history: [
            {
                id: '18-1',
                author: 'a.grober',
                timestamp: '2021-11-09T20:53:00.000Z',
                fieldChanges: {
                    State: {
                        removedValues: {
                            type: 'state',
                            value: 'Open'
                        },
                        addedValues: {
                            type: 'state',
                            value: 'To Verify'
                        }
                    }
                }
            },
            {
                id: '18-2',
                author: 'root',
                timestamp: '2021-11-10T11:24:00.000Z',
                fieldChanges: {
                    State: {
                        removedValues: {
                            type: 'state',
                            value: 'To Verify'
                        },
                        addedValues: {
                            type: 'state',
                            value: 'Verified'
                        }
                    }
                }
            }
        ]
    },
    {
        id: 19,
        key: 'R-19',
        fields: {
            summary: {
                type: 'string',
                value: 'Гайды. Реализовать привязку гайда к городу и шагу'
            },
            description: {
                type: 'text',
                value: 'Реализовать реляции для связи сущности гайда с городом (1-к-1) и шагом (ссылка на справочник, 1-к-1)'
            },
            author: 's.benfetima',
            created: '2021-11-04T14:24:00.000Z',
            updated: '2021-11-12T11:32:00.000Z',
            updatedBy: 'root',
            State: {
                type: 'state',
                value: 'Verified'
            },
            Type: {
                type: 'enum',
                value: 'Task'
            },
            'Исполнитель': {
                type: 'user',
                value: 'a.grober'
            },
            links: [{
                linkName: 'Subtask',
                target: '16'
            }],
        },
        history: [
            {
                id: '19-1',
                author: 'a.grober',
                timestamp: '2021-11-12T10:12:00.000Z',
                fieldChanges: {
                    State: {
                        removedValues: {
                            type: 'state',
                            value: 'Open'
                        },
                        addedValues: {
                            type: 'state',
                            value: 'To Verify'
                        }
                    }
                }
            },
            {
                id: '19-2',
                author: 'e.baranova',
                timestamp: '2021-11-12T11:32:00.000Z',
                fieldChanges: {
                    State: {
                        removedValues: {
                            type: 'state',
                            value: 'To Verify'
                        },
                        addedValues: {
                            type: 'state',
                            value: 'Verified'
                        }
                    }
                }
            }
        ]
    },
    {
        id: 20,
        key: 'R-20',
        fields: {
            summary: {
                type: 'string',
                value: 'Гайды. Реализовать методы добавления и обновления гайда'
            },
            description: {
                type: 'text',
                value: 'Реализовать методы для добавления и обновления гайда. На вход будет приходить название и текст гайда, а так же идентификатор шага и города. Возвращать модель созданного или обновленного гайда'
            },
            author: 's.benfetima',
            created: '2021-11-04T15:48:00.000Z',
            updated: '2021-11-12T14:02:00.000Z',
            updatedBy: 'e.baranova',
            State: {
                type: 'state',
                value: 'Verified'
            },
            Type: {
                type: 'enum',
                value: 'Task'
            },
            'Исполнитель': {
                type: 'user',
                value: 'a.grober'
            },
            links: [{
                linkName: 'Subtask',
                target: '16'
            }],
        },
        history: [
            {
                id: '20-1',
                author: 'a.grober',
                timestamp: '2021-11-12T12:32:00.000Z',
                fieldChanges: {
                    State: {
                        removedValues: {
                            type: 'state',
                            value: 'Open'
                        },
                        addedValues: {
                            type: 'state',
                            value: 'To Verify'
                        }
                    }
                }
            },
            {
                id: '20-2',
                author: 'e.baranova',
                timestamp: '2021-11-12T14:02:00.000Z',
                fieldChanges: {
                    State: {
                        removedValues: {
                            type: 'state',
                            value: 'To Verify'
                        },
                        addedValues: {
                            type: 'state',
                            value: 'Verified'
                        }
                    }
                }
            }
        ]
    },
    {
        id: 21,
        key: 'R-21',
        fields: {
            summary: {
                type: 'string',
                value: 'Гайды. Реализовать метод загрузки файла и привязки его к гайду'
            },
            description: {
                type: 'text',
                value: 'На вход получаем идентификатор гайда и файл. Генерируем запись в таблице файлов, файл сохраняем и ююидом записи в директории с идентификатором гайда'
            },
            author: 's.benfetima',
            created: '2021-11-04T16:41:00.000Z',
            updated: '2021-11-12T14:02:00.000Z',
            updatedBy: 'a.grober',
            State: {
                type: 'state',
                value: 'To Verify'
            },
            Type: {
                type: 'enum',
                value: 'Task'
            },
            'Исполнитель': {
                type: 'user',
                value: 'a.grober'
            },
            links: [{
                linkName: 'Subtask',
                target: '16'
            }],
        },
        history: [
            {
                id: '21-1',
                author: 'a.grober',
                timestamp: '2021-11-12T14:02:00.000Z',
                fieldChanges: {
                    State: {
                        removedValues: {
                            type: 'state',
                            value: 'Open'
                        },
                        addedValues: {
                            type: 'state',
                            value: 'To Verify'
                        }
                    }
                }
            }
        ]
    },
    {
        id: 22,
        key: 'R-22',
        fields: {
            summary: {
                type: 'string',
                value: 'Гайды. Метод сохранения лайка'
            },
            description: {
                type: 'text',
                value: 'На вход получаем идентификатор гайда. Выполняем проверку по ip лайкал пользователь данный гайд или нет. Выполняем проверку по куке. Если обе проверки успешны, добавляем лайк к гайду, записываем айпи в таблицу и передаем на фронт идентификатор для установки куки'
            },
            author: 's.benfetima',
            created: '2021-11-04T17:03:00.000Z',
            updated: '2021-11-12T18:57:00.000Z',
            updatedBy: 'a.grober',
            State: {
                type: 'state',
                value: 'To Verify'
            },
            Type: {
                type: 'enum',
                value: 'Task'
            },
            'Исполнитель': {
                type: 'user',
                value: 'a.grober'
            },
            links: [{
                linkName: 'Subtask',
                target: '16'
            }],
        },
        history: [
            {
                id: '22-1',
                author: 'a.grober',
                timestamp: '2021-11-12T18:57:00.000Z',
                fieldChanges: {
                    State: {
                        removedValues: {
                            type: 'state',
                            value: 'Open'
                        },
                        addedValues: {
                            type: 'state',
                            value: 'To Verify'
                        }
                    }
                }
            }
        ]
    },
    {
        id: 23,
        key: 'R-23',
        fields: {
            summary: {
                type: 'string',
                value: 'Гайды. получение для морды фильтрация по городу'
            },
            description: {
                type: 'text',
                value: 'Реализовать публичный открытый метод для получения гайдов доступных для города. На вход принимаем идентификатор города'
            },
            author: 's.benfetima',
            created: '2021-11-04T17:48:00.000Z',
            updated: '2021-11-15T13:51:00.000Z',
            updatedBy: 'a.grober',
            State: {
                type: 'state',
                value: 'Verified'
            },
            Type: {
                type: 'enum',
                value: 'Task'
            },
            'Исполнитель': {
                type: 'user',
                value: 'a.grober'
            },
            links: [{
                linkName: 'Subtask',
                target: '16'
            }],
        },
        history: [
            {
                id: '23-1',
                author: 'a.grober',
                timestamp: '2021-11-15T10:11:00.000Z',
                fieldChanges: {
                    State: {
                        removedValues: {
                            type: 'state',
                            value: 'Open'
                        },
                        addedValues: {
                            type: 'state',
                            value: 'To Verify'
                        }
                    }
                }
            },
            {
                id: '23-2',
                author: 's.benfetima',
                timestamp: '2021-11-15T13:51:00.000Z',
                fieldChanges: {
                    State: {
                        removedValues: {
                            type: 'state',
                            value: 'To Verify'
                        },
                        addedValues: {
                            type: 'state',
                            value: 'Verified'
                        }
                    }
                }
            }
        ]
    },
    {
        id: 24,
        key: 'R-24',
        fields: {
            summary: {
                type: 'string',
                value: 'Произвольные статьи. Список'
            },
            description: {
                type: 'text',
                value: 'Реализовать публичный открытый метод для получения списка произвольных свободных статей. Возвращать с пагинацией'
            },
            author: 's.benfetima',
            created: '2021-11-04T17:59:00.000Z',
            updated: '2021-11-15T17:33:00.000Z',
            updatedBy: 's.benfetima',
            State: {
                type: 'state',
                value: 'Verified'
            },
            Type: {
                type: 'enum',
                value: 'Task'
            },
            'Исполнитель': {
                type: 'user',
                value: 'a.grober'
            },
            links: [{
                linkName: 'Subtask',
                target: '16'
            }],
        },
        history: [
            {
                id: '24-1',
                author: 'a.grober',
                timestamp: '2021-11-15T15:24:00.000Z',
                fieldChanges: {
                    State: {
                        removedValues: {
                            type: 'state',
                            value: 'Open'
                        },
                        addedValues: {
                            type: 'state',
                            value: 'To Verify'
                        }
                    }
                }
            },
            {
                id: '24-2',
                author: 's.benfetima',
                timestamp: '2021-11-15T17:33:00.000Z',
                fieldChanges: {
                    State: {
                        removedValues: {
                            type: 'state',
                            value: 'To Verify'
                        },
                        addedValues: {
                            type: 'state',
                            value: 'Verified'
                        }
                    }
                }
            }
        ]
    },
    {
        id: 25,
        key: 'R-25',
        fields: {
            summary: {
                type: 'string',
                value: 'Произвольные статьи. Управление'
            },
            description: {
                type: 'text',
                value: 'Реализовать методы добавления, обновления и удаления статей. Методы доступны только по запросу админ-серсвиса с админским токеном'
            },
            author: 's.benfetima',
            created: '2021-11-05T12:23:00.000Z',
            updated: '2021-11-15T20:48:00.000Z',
            updatedBy: 's.benfetima',
            State: {
                type: 'state',
                value: 'Verified'
            },
            Type: {
                type: 'enum',
                value: 'Task'
            },
            'Исполнитель': {
                type: 'user',
                value: 'a.grober'
            },
            links: [{
                linkName: 'Subtask',
                target: '16'
            }],
        },
        history: [
            {
                id: '25-1',
                author: 'a.grober',
                timestamp: '2021-11-15T18:40:00.000Z',
                fieldChanges: {
                    State: {
                        removedValues: {
                            type: 'state',
                            value: 'Open'
                        },
                        addedValues: {
                            type: 'state',
                            value: 'To Verify'
                        }
                    }
                }
            },
            {
                id: '25-2',
                author: 's.benfetima',
                timestamp: '2021-11-15T20:48:00.000Z',
                fieldChanges: {
                    State: {
                        removedValues: {
                            type: 'state',
                            value: 'To Verify'
                        },
                        addedValues: {
                            type: 'state',
                            value: 'Verified'
                        }
                    }
                }
            }
        ]
    },
    {
        id: 26,
        key: 'R-26',
        fields: {
            summary: {
                type: 'string',
                value: 'Произвольные статьи. Проксирующие методы в админ-сервисе'
            },
            description: {
                type: 'text',
                value: 'Реализовать проксирующие методы в админ-сервисе для добавления, обновления и удаления статей. БЛ на стороне контент-сервиса'
            },
            author: 's.benfetima',
            created: '2021-11-05T12:50:00.000Z',
            updated: '2021-11-15T21:03:00.000Z',
            updatedBy: 's.benfetima',
            State: {
                type: 'state',
                value: 'Verified'
            },
            Type: {
                type: 'enum',
                value: 'Task'
            },
            'Исполнитель': {
                type: 'user',
                value: 'a.grober'
            },
            links: [{
                linkName: 'Subtask',
                target: '16'
            }],
        },
        history: [
            {
                id: '26-1',
                author: 'a.grober',
                timestamp: '2021-11-15T19:31:00.000Z',
                fieldChanges: {
                    State: {
                        removedValues: {
                            type: 'state',
                            value: 'Open'
                        },
                        addedValues: {
                            type: 'state',
                            value: 'To Verify'
                        }
                    }
                }
            },
            {
                id: '26-2',
                author: 's.benfetima',
                timestamp: '2021-11-15T21:03:00.000Z',
                fieldChanges: {
                    State: {
                        removedValues: {
                            type: 'state',
                            value: 'To Verify'
                        },
                        addedValues: {
                            type: 'state',
                            value: 'Verified'
                        }
                    }
                }
            }
        ]
    },
    {
        id: 27,
        key: 'R-27',
        fields: {
            summary: {
                type: 'string',
                value: 'Реализовать метод подсчета рейтинга гайда'
            },
            description: {
                type: 'text',
                value: 'Реализовать рассчет рейтнга. На паузе, обсудить с Сергеем реализацию, очередь, реал-тайм или кронтаб'
            },
            author: 's.benfetima',
            created: '2021-11-08T13:21:00.000Z',
            updated: '2021-11-08T13:21:00.000Z',
            updatedBy: 's.benfetima',
            State: {
                type: 'state',
                value: 'Open'
            },
            Type: {
                type: 'enum',
                value: 'Task'
            },
            'Исполнитель': {
                type: 'user',
                value: 'a.grober'
            },
            links: [{
                linkName: 'Subtask',
                target: '16'
            }],
        },
        history: []
    },

    {
        id: 28,
        key: 'R-28',
        fields: {
            summary: {
                type: 'string',
                value: 'Главный сервис'
            },
            description: {
                type: 'text',
                value: ''
            },
            author: 'root',
            created: '2021-10-21T13:03:10.000Z',
            updated: '2021-10-21T13:03:10.000Z',
            updatedBy: 'root',
            State: {
                type: 'state',
                value: 'Open'
            },
            Type: {
                type: 'enum',
                value: 'User Story'
            }
        },
        history: []
    },
    {
        id: 29,
        key: 'R-29',
        fields: {
            summary: {
                type: 'string',
                value: 'Авторизация'
            },
            description: {
                type: 'text',
                value: 'Все полностью как в галерее. Проверить и при необходимости обновить контроллеры и методы до последней версии с учетом исправлений всех ошибок. Проверить постманом или свагером что все работают. Вычистить то что касается регистрации через ХД из классов, контроллеров и бд.'
            },
            author: 's.benfetima',
            created: '2021-11-06T19:48:00.000Z',
            updated: '2021-11-13T18:20:00.000Z',
            updatedBy: 'root',
            State: {
                type: 'state',
                value: 'Verified'
            },
            Type: {
                type: 'enum',
                value: 'Task'
            },
            'Исполнитель': {
                type: 'user',
                value: 'a.grober'
            },
            links: [{
                linkName: 'Subtask',
                target: '28'
            }],
        },
        history: [
            {
                id: '29-1',
                author: 'a.grober',
                timestamp: '2021-11-13T11:26:00.000Z',
                fieldChanges: {
                    State: {
                        removedValues: {
                            type: 'state',
                            value: 'Open'
                        },
                        addedValues: {
                            type: 'state',
                            value: 'To Verify'
                        }
                    }
                }
            },
            {
                id: '29-2',
                author: 's.benfetima',
                timestamp: '2021-11-13T18:20:00.000Z',
                fieldChanges: {
                    State: {
                        removedValues: {
                            type: 'state',
                            value: 'To Verify'
                        },
                        addedValues: {
                            type: 'state',
                            value: 'Verified'
                        }
                    }
                }
            }
        ]
    },
    {
        id: 30,
        key: 'R-30',
        fields: {
            summary: {
                type: 'string',
                value: 'Регистрация'
            },
            description: {
                type: 'text',
                value: 'Все полностью как в галерее. Проверить и при необходимости обновить контроллеры и методы до последней версии с учетом исправлений всех ошибок. Проверить постманом или свагером что все работают. Вычистить то что касается регистрации через ХД'
            },
            author: 's.benfetima',
            created: '2021-11-06T20:04:00.000Z',
            updated: '2021-11-13T18:45:00.000Z',
            updatedBy: 'root',
            State: {
                type: 'state',
                value: 'Verified'
            },
            Type: {
                type: 'enum',
                value: 'Task'
            },
            'Исполнитель': {
                type: 'user',
                value: 'a.grober'
            },
            links: [{
                linkName: 'Subtask',
                target: '28'
            }],
        },
        history: [
            {
                id: '30-1',
                author: 'a.grober',
                timestamp: '2021-11-13T14:15:00.000Z',
                fieldChanges: {
                    State: {
                        removedValues: {
                            type: 'state',
                            value: 'Open'
                        },
                        addedValues: {
                            type: 'state',
                            value: 'To Verify'
                        }
                    }
                }
            },
            {
                id: '30-2',
                author: 's.benfetima',
                timestamp: '2021-11-13T19:03:00.000Z',
                fieldChanges: {
                    State: {
                        removedValues: {
                            type: 'state',
                            value: 'To Verify'
                        },
                        addedValues: {
                            type: 'state',
                            value: 'Verified'
                        }
                    }
                }
            }
        ]
    },
    {
        id: 31,
        key: 'R-31',
        fields: {
            summary: {
                type: 'string',
                value: 'Восстановление пароля'
            },
            description: {
                type: 'text',
                value: 'Все полностью как в галерее.Проверить и при необходимости обновить контроллеры и методы до последней версии с учетом исправлений всех ошибок.Проверить постманом или свагером что все работают.'
            },
            author: 's.benfetima',
            created: '2021-11-06T20:27:00.000Z',
            updated: '2021-11-13T19:53:00.000Z',
            updatedBy: 'root',
            State: {
                type: 'state',
                value: 'Verified'
            },
            Type: {
                type: 'enum',
                value: 'Task'
            },
            'Исполнитель': {
                type: 'user',
                value: 'a.grober'
            },
            links: [{
                linkName: 'Subtask',
                target: '28'
            }],
        },
        history: [
            {
                id: '31-1',
                author: 'a.grober',
                timestamp: '2021-11-13T15:38:00.000Z',
                fieldChanges: {
                    State: {
                        removedValues: {
                            type: 'state',
                            value: 'Open'
                        },
                        addedValues: {
                            type: 'state',
                            value: 'To Verify'
                        }
                    }
                }
            },
            {
                id: '31-2',
                author: 's.benfetima',
                timestamp: '2021-11-13T19:53:00.000Z',
                fieldChanges: {
                    State: {
                        removedValues: {
                            type: 'state',
                            value: 'To Verify'
                        },
                        addedValues: {
                            type: 'state',
                            value: 'Verified'
                        }
                    }
                }
            }
        ]
    },
    {
        id: 32,
        key: 'R-32',
        fields: {
            summary: {
                type: 'string',
                value: 'Объект мембера'
            },
            description: {
                type: 'text',
                value: 'Все полностью как в галерее. Проверить и при необходимости обновить контроллеры и методы до последней версии с учетом исправлений всех ошибок. Проверить постманом или свагером что все работают. Вычистить лишнее (то что касается тарифов, источников регистрации, галерей, занимаемого места)'
            },
            author: 's.benfetima',
            created: '2021-11-06T20:45:00.000Z',
            updated: '2021-11-13T20:27:00.000Z',
            updatedBy: 's.benfetima',
            State: {
                type: 'state',
                value: 'Verified'
            },
            Type: {
                type: 'enum',
                value: 'Task'
            },
            'Исполнитель': {
                type: 'user',
                value: 'a.grober'
            },
            links: [{
                linkName: 'Subtask',
                target: '28'
            }],
        },
        history: [
            {
                id: '32-1',
                author: 'a.grober',
                timestamp: '2021-11-13T17:20:00.000Z',
                fieldChanges: {
                    State: {
                        removedValues: {
                            type: 'state',
                            value: 'Open'
                        },
                        addedValues: {
                            type: 'state',
                            value: 'To Verify'
                        }
                    }
                }
            },
            {
                id: '32-2',
                author: 's.benfetima',
                timestamp: '2021-11-13T20:27:00.000Z',
                fieldChanges: {
                    State: {
                        removedValues: {
                            type: 'state',
                            value: 'To Verify'
                        },
                        addedValues: {
                            type: 'state',
                            value: 'Verified'
                        }
                    }
                }
            }
        ]
    },
    {
        id: 33,
        key: 'R-33',
        fields: {
            summary: {
                type: 'string',
                value: 'Метод получения публичной информации о профиле'
            },
            description: {
                type: 'text',
                value: 'На вход принимается идентификатор профиля. Возвращает консолидированную публичную информацию о профиле агента подмассивами: данные профиля, контактные данные, услуги'
            },
            author: 's.benfetima',
            created: '2021-11-07T11:31:00.000Z',
            updated: '2021-11-22T15:04:00.000Z',
            updatedBy: 's.benfetima',
            State: {
                type: 'state',
                value: 'Verified'
            },
            Type: {
                type: 'enum',
                value: 'Task'
            },
            'Исполнитель': {
                type: 'user',
                value: 'a.grober'
            },
            links: [{
                linkName: 'Subtask',
                target: '28'
            }],
        },
        history: [
            {
                id: '33-1',
                author: 'a.grober',
                timestamp: '2021-11-21T14:21:00.000Z',
                fieldChanges: {
                    State: {
                        removedValues: {
                            type: 'state',
                            value: 'Open'
                        },
                        addedValues: {
                            type: 'state',
                            value: 'To Verify'
                        }
                    }
                }
            },
            {
                id: '33-2',
                author: 's.benfetima',
                timestamp: '2021-11-22T15:04:00.000Z',
                fieldChanges: {
                    State: {
                        removedValues: {
                            type: 'state',
                            value: 'To Verify'
                        },
                        addedValues: {
                            type: 'state',
                            value: 'Verified'
                        }
                    }
                }
            }
        ]
    },
    {
        id: 34,
        key: 'R-34',
        fields: {
            summary: {
                type: 'string',
                value: 'Получение списка профилей и фильтрация'
            },
            description: {
                type: 'text',
                value: 'Реализовать метод для получения списка профилей. На вход принимается массив filters с городом и кладбищем для поиска. Реализовать с базовой пагинацией'
            },
            author: 's.benfetima',
            created: '2021-11-07T11:31:00.000Z',
            updated: '2021-11-24T18:24:00.000Z',
            updatedBy: 'a.grober',
            State: {
                type: 'state',
                value: 'To Verify'
            },
            Type: {
                type: 'enum',
                value: 'Task'
            },
            'Исполнитель': {
                type: 'user',
                value: 'a.grober'
            },
            links: [{
                linkName: 'Subtask',
                target: '28'
            }],
        },
        history: [
            {
                id: '34-1',
                author: 'a.grober',
                timestamp: '2021-11-24T18:24:00.000Z',
                fieldChanges: {
                    State: {
                        removedValues: {
                            type: 'state',
                            value: 'Open'
                        },
                        addedValues: {
                            type: 'state',
                            value: 'To Verify'
                        }
                    }
                }
            }
        ]
    },
    {
        id: 35,
        key: 'R-35',
        fields: {
            summary: {
                type: 'string',
                value: 'Профиль агента. Вход и безопасность.'
            },
            description: {
                type: 'text',
                value: 'Реализовать набор методов для раздела вход и безопастность (изменение номера, изменение пароля, время обновления пароля). Все методы и БИ аналогичны галерейным. Вычистить реализацию ХД oauth'
            },
            author: 's.benfetima',
            created: '2021-11-07T12:20:00.000Z',
            updated: '2021-11-27T12:05:00.000Z',
            updatedBy: 'a.grober',
            State: {
                type: 'state',
                value: 'To Verify'
            },
            Type: {
                type: 'enum',
                value: 'Task'
            },
            'Исполнитель': {
                type: 'user',
                value: 'a.grober'
            },
            links: [{
                linkName: 'Subtask',
                target: '28'
            }],
        },
        history: [
            {
                id: '35-1',
                author: 'a.grober',
                timestamp: '2021-11-27T12:05:00.000Z',
                fieldChanges: {
                    State: {
                        removedValues: {
                            type: 'state',
                            value: 'Open'
                        },
                        addedValues: {
                            type: 'state',
                            value: 'To Verify'
                        }
                    }
                }
            }
        ]
    },
    {
        id: 36,
        key: 'R-36',
        fields: {
            summary: {
                type: 'string',
                value: 'Профиль. Обновление основной информации'
            },
            description: {
                type: 'text',
                value: 'Реализовать метод для обновления информации профиля. На вход принимается фио, описание и флаг наличия договора. Информация обновляется по текущему мемберу'
            },
            author: 's.benfetima',
            created: '2021-11-07T12:15:00.000Z',
            updated: '2021-11-27T16:53:00.000Z',
            updatedBy: 'a.grober',
            State: {
                type: 'state',
                value: 'To Verify'
            },
            Type: {
                type: 'enum',
                value: 'Task'
            },
            'Исполнитель': {
                type: 'user',
                value: 'a.grober'
            },
            links: [{
                linkName: 'Subtask',
                target: '28'
            }],
        },
        history: [
            {
                id: '36-1',
                author: 'a.grober',
                timestamp: '2021-11-27T16:53:00.000Z',
                fieldChanges: {
                    State: {
                        removedValues: {
                            type: 'state',
                            value: 'Open'
                        },
                        addedValues: {
                            type: 'state',
                            value: 'To Verify'
                        }
                    }
                }
            }
        ]
    },
    {
        id: 37,
        key: 'R-37',
        fields: {
            summary: {
                type: 'string',
                value: 'Профиль. Аватарка'
            },
            description: {
                type: 'text',
                value: 'Портировать из галереи методы имейдж-сервиса для кропа, сохранения и изменения аватарки. Аватарка аналогично хд или гл привязывается к профилю. Методы остаются не изменными, меняются только роуты в соответсвии с сервисом'
            },
            author: 's.benfetima',
            created: '2021-11-07T12:59:00.000Z',
            updated: '2021-11-28T15:20:00.000Z',
            updatedBy: 'a.grober',
            State: {
                type: 'state',
                value: 'To Verify'
            },
            Type: {
                type: 'enum',
                value: 'Task'
            },
            'Исполнитель': {
                type: 'user',
                value: 'a.grober'
            },
            links: [{
                linkName: 'Subtask',
                target: '28'
            }],
        },
        history: [
            {
                id: '37-1',
                author: 'a.grober',
                timestamp: '2021-11-28T15:20:00.000Z',
                fieldChanges: {
                    State: {
                        removedValues: {
                            type: 'state',
                            value: 'Open'
                        },
                        addedValues: {
                            type: 'state',
                            value: 'To Verify'
                        }
                    }
                }
            }
        ]
    },
    {
        id: 38,
        key: 'R-38',
        fields: {
            summary: {
                type: 'string',
                value: 'Профиль. Публикация и проверка доступности публикации'
            },
            description: {
                type: 'text',
                value: 'Обсудить с Сергеем или уточнить с Костей правила публикации. '
            },
            author: 's.benfetima',
            created: '2021-11-07T15:27:00.000Z',
            updated: '2021-11-07T15:27:00.000Z',
            updatedBy: 's.benfetima',
            State: {
                type: 'state',
                value: 'To Verify'
            },
            Type: {
                type: 'enum',
                value: 'Task'
            },
            'Исполнитель': {
                type: 'user',
                value: 's.benfetima'
            },
            links: [{
                linkName: 'Subtask',
                target: '28'
            }],
        },
        history: []
    },
];
const projects = [{
    id: 'R1',
    name: "Rituals",
    key: 'RTL'
}

];

const JsonClient = function(context) {
    const client = {};

    const getTimestampFormats = () => {
        return ["yyyy-MM-dd'T'HH:mm:ss'Z'", "yyyy-MM-dd'T'HH:mm:ss.SSS'Z'", "yyyy-MM-dd"];
    };

    const getServerInfo = () => {
        return {
            version: 'json',
            time: new Date().toISOString()
        };
    };

    Object.assign(client, {
        getTimestampFormats: getTimestampFormats.bind(this),
        getServerInfo: getServerInfo.bind(this),
        getAttachmentContent: () => {},
        getUsers: () => [],
        // Optional:
        getProjects: () => projects,
        getIssues: () => issues
    });
    return client;
};

exports.Client = JsonClient;
